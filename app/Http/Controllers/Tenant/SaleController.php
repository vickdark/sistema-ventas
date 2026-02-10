<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Sale;
use App\Models\Tenant\SaleItem;
use App\Models\Tenant\Product;
use App\Models\Tenant\Client;
use App\Models\Tenant\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = Sale::with(['client', 'user']);

            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nro_venta', 'like', "%{$search}%")
                      ->orWhere('voucher', 'like', "%{$search}%")
                      ->orWhereHas('client', function($cq) use ($search) {
                          $cq->where('name', 'like', "%{$search}%");
                      });
                });
            }

            $total = $query->count();
            
            $sales = $query->orderBy('id', 'desc')
                           ->offset($offset)
                           ->limit($limit)
                           ->get();

            return response()->json([
                'data' => $sales,
                'total' => (int) $total,
                'status' => 'success'
            ]);
        }
        return view('tenant.sales.index');
    }

    public function create()
    {
        // Check if there is an open cash register
        $cashRegister = CashRegister::open()->first();
        if (!$cashRegister) {
            return redirect()->route('cash-registers.index')
                ->with('error', 'Debe abrir una caja antes de realizar ventas.');
        }

        $clients = Client::all();
        $products = Product::where('stock', '>', 0)->get();
        $lastSale = Sale::latest()->first();
        $nextNroVenta = $lastSale ? $lastSale->nro_venta + 1 : 1;

        return view('tenant.sales.create', compact('clients', 'products', 'nextNroVenta'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'payment_type' => 'required|in:CONTADO,TRANSFERENCIA,CREDITO',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'voucher' => 'nullable|string|max:255',
        ]);

        $cashRegister = CashRegister::open()->first();
        if (!$cashRegister) {
            return response()->json(['message' => 'Caja cerrada.'], 403);
        }

        return DB::transaction(function () use ($request) {
            $total = 0;
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stock insuficiente para el producto: {$product->name}");
                }
                $total += $product->sale_price * $item['quantity'];
            }

            $paymentStatus = in_array($request->payment_type, ['CONTADO', 'TRANSFERENCIA']) ? 'PAGADO' : 'PENDIENTE';
            
            $lastSale = Sale::latest()->first();
            $nroVenta = $lastSale ? $lastSale->nro_venta + 1 : 1;

            $sale = Sale::create([
                'nro_venta' => $nroVenta,
                'client_id' => $request->client_id,
                'total_paid' => $total,
                'user_id' => auth()->id(),
                'sale_date' => now(),
                'voucher' => $request->voucher,
                'payment_type' => $request->payment_type,
                'payment_status' => $paymentStatus,
                'credit_payment_date' => $request->payment_type === 'CREDITO' ? $request->credit_payment_date : null,
            ]);

            foreach ($request->items as $itemData) {
                $product = Product::find($itemData['product_id']);
                
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'client_id' => $request->client_id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $product->sale_price,
                    'sale_date' => now(),
                    'voucher' => $request->voucher,
                ]);

                // Update stock
                $product->stock -= $itemData['quantity'];
                $product->save();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Venta registrada correctamente.',
                'sale_id' => $sale->id
            ]);
        });
    }

    public function show(string $id)
    {
        $sale = Sale::with(['client', 'user', 'items.product'])->findOrFail($id);
        return view('tenant.sales.show', compact('sale'));
    }

    public function destroy(string $id)
    {
        return DB::transaction(function () use ($id) {
            $sale = Sale::with('items')->findOrFail($id);
            
            foreach ($sale->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->stock += $item->quantity;
                    $product->save();
                }
            }

            $sale->delete();
            return redirect()->route('sales.index')->with('success', 'Venta eliminada y stock restaurado.');
        });
    }

    public function ticket(Sale $sale)
    {
        return view('tenant.sales.ticket', compact('sale'));
    }
}
