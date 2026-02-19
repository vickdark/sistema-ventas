<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Product;
use App\Models\Tenant\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = Product::with('category');

            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }

            $total = $query->count();
            $products = $query->orderBy('stock', 'asc')
                              ->offset($offset)
                              ->limit($limit)
                              ->get();

            return response()->json([
                'data' => $products,
                'total' => (int) $total,
                'status' => 'success'
            ]);
        }

        $config = [
            'routes' => [
                'index' => route('inventory.index'),
                'adjust' => route('inventory.adjust'),
                'kardex' => route('inventory.kardex', ':id')
            ]
        ];

        return view('tenant.inventory.index', compact('config'));
    }

    public function kardex(Product $product)
    {
        $movements = StockMovement::where('product_id', $product->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('tenant.inventory.kardex', compact('product', 'movements'));
    }

    public function adjust(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:input,output',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($request) {
            $product = Product::findOrFail($request->product_id);
            $prevStock = $product->stock;
            
            if ($request->type === 'input') {
                $product->stock += $request->quantity;
            } else {
                if ($product->stock < $request->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stock insuficiente para realizar el ajuste.'
                    ], 422);
                }
                $product->stock -= $request->quantity;
            }

            $product->save();

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => $request->type,
                'quantity' => $request->quantity,
                'reason' => 'Ajuste Manual: ' . $request->reason,
                'description' => $request->description,
                'prev_stock' => $prevStock,
                'new_stock' => $product->stock,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Inventario ajustado correctamente.',
                'new_stock' => $product->stock
            ]);
        });
    }
}
