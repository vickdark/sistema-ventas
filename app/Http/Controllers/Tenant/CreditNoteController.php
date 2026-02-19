<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\CreditNote;
use App\Models\Tenant\CreditNoteItem;
use App\Models\Tenant\Sale;
use App\Models\Tenant\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreditNoteController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = CreditNote::with(['sale.client', 'user']);

            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('number', 'like', "%{$search}%")
                      ->orWhere('reason', 'like', "%{$search}%")
                      ->orWhereHas('sale', function($sq) use ($search) {
                          $sq->where('nro_venta', 'like', "%{$search}%");
                      });
                });
            }

            $total = $query->count();
            
            $notes = $query->orderBy('id', 'desc')
                           ->offset($offset)
                           ->limit($limit)
                           ->get();

            return response()->json([
                'data' => $notes,
                'total' => (int) $total,
                'status' => 'success'
            ]);
        }
        
        $config = [
            'routes' => [
                'index' => route('credit-notes.index'),
                'show' => route('credit-notes.show', ':id'),
                'destroy' => route('credit-notes.destroy', ':id')
            ]
        ];

        return view('tenant.credit_notes.index', compact('config'));
    }

    public function create(Request $request)
    {
        $sale = Sale::with(['items.product', 'client'])->findOrFail($request->sale_id);
        
        // Verificar si la venta tiene abonos o estados que impidan la nota de crédito
        // Por ahora lo permitimos siempre.

        $lastNote = CreditNote::latest()->first();
        $nextNumber = $lastNote ? (int)str_replace('NC-', '', $lastNote->number) + 1 : 1;
        $nextNumber = 'NC-' . str_pad((string)$nextNumber, 6, '0', STR_PAD_LEFT);

        return view('tenant.credit_notes.create', compact('sale', 'nextNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'reason' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            $sale = Sale::with('items')->findOrFail($request->sale_id);
            $totalNote = 0;

            $lastNote = CreditNote::latest()->first();
            $nextNumber = $lastNote ? (int)str_replace('NC-', '', $lastNote->number) + 1 : 1;
            $number = 'NC-' . str_pad((string)$nextNumber, 6, '0', STR_PAD_LEFT);

            $reason = $request->reason === 'Otro' ? $request->other_reason : $request->reason;

            $creditNote = CreditNote::create([
                'number' => $number,
                'sale_id' => $sale->id,
                'user_id' => Auth::id(),
                'reason' => $reason,
                'total' => 0, // Se actualizará al final
                'status' => 'active',
            ]);

            foreach ($request->items as $itemData) {
                $saleItem = $sale->items()->where('product_id', $itemData['product_id'])->first();
                
                if (!$saleItem) {
                    throw new \Exception("El producto seleccionado no pertenece a la venta original.");
                }

                if ($itemData['quantity'] > $saleItem->quantity) {
                    throw new \Exception("La cantidad devuelta no puede ser mayor a la vendida.");
                }

                $subtotal = $saleItem->price * $itemData['quantity'];
                $totalNote += $subtotal;

                CreditNoteItem::create([
                    'credit_note_id' => $creditNote->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $saleItem->price,
                    'subtotal' => $subtotal,
                    'restock' => $request->has('restock') ? true : true, // Por defecto siempre reingresa
                ]);

                // Restaurar Stock using helper
                $product = Product::find($itemData['product_id']);
                if ($product) {
                    $product->addStock($itemData['quantity'], 'Devolución', 'NC #' . $creditNote->number, $creditNote);
                }
            }

            $creditNote->update(['total' => $totalNote]);

            return redirect()->route('credit-notes.index')
                ->with('success', "Nota de Crédito {$number} generada correctamente.");
        });
    }

    public function show($id)
    {
        $note = CreditNote::with(['sale.client', 'user', 'items.product'])->findOrFail($id);
        return view('tenant.credit_notes.show', compact('note'));
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $note = CreditNote::with('items')->findOrFail($id);
            
            if ($note->status === 'void') {
                return back()->with('error', 'Esta nota ya está anulada.');
            }

            foreach ($note->items as $item) {
                // Si se restauró stock, ahora hay que quitarlo al anular la devolución
                $product = Product::find($item->product_id);
                if ($product && $item->restock) {
                    $product->removeStock($item->quantity, 'Anulación de Devolución', 'NC #' . $note->number . ' anulada');
                }
            }

            $note->update(['status' => 'void']);

            return redirect()->route('credit-notes.index')
                ->with('success', 'Nota de crédito anulada y stock ajustado.');
        });
    }
}
