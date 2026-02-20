<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\StockTransfer;
use App\Models\Tenant\StockTransferItem;
use App\Models\Tenant\Product;
use App\Models\Tenant\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = StockTransfer::with(['originBranch', 'destinationBranch', 'user']);

            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nro_traslado', 'like', "%{$search}%")
                      ->orWhereHas('originBranch', function($oq) use ($search) {
                          $oq->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('destinationBranch', function($dq) use ($search) {
                          $dq->where('name', 'like', "%{$search}%");
                      });
                });
            }

            $total = $query->count();
            
            $transfers = $query->orderBy('id', 'desc')
                               ->offset($offset)
                               ->limit($limit)
                               ->get();

            return response()->json([
                'data' => $transfers->map(function($transfer) {
                    return [
                        'id' => $transfer->id,
                        'nro_traslado' => $transfer->nro_traslado,
                        'origin' => $transfer->originBranch->name,
                        'destination' => $transfer->destinationBranch->name,
                        'user' => $transfer->user->name,
                        'status' => $transfer->status,
                        'date' => $transfer->shipped_at ? $transfer->shipped_at->format('d/m/Y H:i') : '-',
                    ];
                }),
                'total' => (int) $total,
                'status' => 'success'
            ]);
        }
        
        $config = [
            'routes' => [
                'index' => route('stock-transfers.index'),
                'show' => route('stock-transfers.show', ':id'),
                'create' => route('stock-transfers.create'),
                'receive' => route('stock-transfers.receive', ':id'),
            ]
        ];

        return view('tenant.stock_transfers.index', compact('config'));
    }

    public function create()
    {
        $branches = Branch::where('id', '!=', session('active_branch_id'))->get();
        $products = Product::where('stock', '>', 0)->with('category')->get();
        $lastTransfer = StockTransfer::latest()->first();
        $nextNroTraslado = $lastTransfer ? (int)$lastTransfer->nro_traslado + 1 : 1;
        
        $config = [
            'routes' => [
                'store' => route('stock-transfers.store'),
                'index' => route('stock-transfers.index'),
            ]
        ];

        return view('tenant.stock_transfers.create', compact('branches', 'products', 'nextNroTraslado', 'config'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'destination_branch_id' => 'required|exists:branches,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $originBranchId = session('active_branch_id');
            
            if ($originBranchId == $request->destination_branch_id) {
                return response()->json(['message' => 'La sucursal de destino debe ser diferente a la de origen.'], 422);
            }

            $lastTransfer = StockTransfer::latest()->first();
            $nroTraslado = $lastTransfer ? (int)$lastTransfer->nro_traslado + 1 : 1;

            $transfer = StockTransfer::create([
                'nro_traslado' => (string)$nroTraslado,
                'origin_branch_id' => $originBranchId,
                'destination_branch_id' => $request->destination_branch_id,
                'user_id' => Auth::id(),
                'status' => 'ENVIADO',
                'shipped_at' => now(),
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $itemData) {
                $productOrigin = Product::findOrFail($itemData['product_id']);
                
                if ($productOrigin->stock < $itemData['quantity']) {
                    throw new \Exception("Stock insuficiente para el producto: {$productOrigin->name}");
                }

                // Deduct stock from origin
                $productOrigin->removeStock($itemData['quantity'], 'Traslado (Envío)', 'Hacia Sucursal ID #' . $request->destination_branch_id, $transfer);

                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id' => $productOrigin->id,
                    'quantity' => $itemData['quantity']
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Traslado enviado correctamente.',
                'transfer_id' => $transfer->id
            ]);
        });
    }

    public function show($id)
    {
        $transfer = StockTransfer::with(['originBranch', 'destinationBranch', 'user', 'items.product'])->findOrFail($id);
        return view('tenant.stock_transfers.show', compact('transfer'));
    }

    public function receive($id)
    {
        $transfer = StockTransfer::with('items.product')->findOrFail($id);
        
        if ($transfer->status !== 'ENVIADO') {
            return redirect()->back()->with('error', 'Este traslado no puede ser recibido en su estado actual.');
        }

        // Verify that the user is in the destination branch
        if (session('active_branch_id') != $transfer->destination_branch_id) {
            return redirect()->back()->with('error', 'Debes estar en la sucursal de destino para recibir los productos.');
        }

        try {
            return DB::transaction(function () use ($transfer) {
                foreach ($transfer->items as $item) {
                    $productOrigin = $item->product;
                    
                    // Find corresponding product in destination branch by code
                    $productDest = Product::withoutGlobalScope('branch')
                        ->where('branch_id', $transfer->destination_branch_id)
                        ->where('code', $productOrigin->code)
                        ->first();

                    if (!$productDest) {
                        // Create product in destination branch if it doesn't exist
                        $productDest = $productOrigin->replicate();
                        $productDest->branch_id = $transfer->destination_branch_id;
                        $productDest->stock = 0;
                        $productDest->save();
                    }

                    $productDest->addStock($item->quantity, 'Traslado (Recepción)', 'Desde Sucursal ID #' . $transfer->origin_branch_id, $transfer);
                }

                $transfer->update([
                    'status' => 'RECIBIDO',
                    'received_at' => now()
                ]);

                return redirect()->route('stock-transfers.index')->with('success', 'Stock recibido y actualizado correctamente.');
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al recibir: ' . $e->getMessage());
        }
    }
}
