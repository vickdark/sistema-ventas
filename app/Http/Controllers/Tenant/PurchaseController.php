<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Purchase;
use App\Models\Tenant\Product;
use App\Models\Tenant\Supplier;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = Purchase::with(['product', 'supplier']);

            // Grid.js parameters
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nro_compra', 'like', "%{$search}%")
                      ->orWhere('voucher', 'like', "%{$search}%")
                      ->orWhereHas('product', function($pq) use ($search) {
                          $pq->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('supplier', function($sq) use ($search) {
                          $sq->where('name', 'like', "%{$search}%");
                      });
                });
            }

            $total = $query->count();
            
            $purchases = $query->orderBy('id', 'desc')
                               ->offset($offset)
                               ->limit($limit)
                               ->get();

            return response()->json([
                'data' => $purchases,
                'total' => (int) $total,
                'status' => 'success'
            ]);
        }
        return view('tenant.purchases.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        $suppliers = Supplier::all();
        $lastPurchase = Purchase::latest()->first();
        $nextNroCompra = $lastPurchase ? (int)$lastPurchase->nro_compra + 1 : 1;

        return view('tenant.purchases.create', compact('products', 'suppliers', 'nextNroCompra'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'quantity' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->product_id) {
                        $product = Product::find($request->product_id);
                        if ($product && $product->max_stock > 0 && ($product->stock + $value) > $product->max_stock) {
                            $fail("La cantidad ingresada supera el stock máximo permitido ({$product->max_stock}). Stock actual: {$product->stock}.");
                        }
                    }
                },
            ],
            'price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'voucher' => 'required|string|max:255',
            'nro_compra' => 'required|unique:purchases,nro_compra',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();

        $purchase = Purchase::create($data);

        // Update product stock
        $product = Product::find($request->product_id);
        $product->stock += $request->quantity;
        $product->save();

        return redirect()->route('purchases.index')
            ->with('success', 'Compra registrada correctamente.')
            ->with('new_purchase_id', $purchase->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $purchase = Purchase::with(['product', 'supplier', 'user'])->findOrFail($id);
        return view('tenant.purchases.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $purchase = Purchase::findOrFail($id);
        $products = Product::all();
        $suppliers = Supplier::all();
        return view('tenant.purchases.edit', compact('purchase', 'products', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $purchase = Purchase::findOrFail($id);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'voucher' => 'required|string|max:255',
            'nro_compra' => 'required|unique:purchases,nro_compra,' . $id,
        ]);

        // Adjust product stock
        $product = Product::find($purchase->product_id);
        $product->stock -= $purchase->quantity; // Revert old quantity
        $product->save();

        $purchase->update($request->all());

        $newProduct = Product::find($request->product_id);
        $newProduct->stock += $request->quantity; // Add new quantity
        $newProduct->save();

        return redirect()->route('purchases.index')->with('success', 'Compra actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $purchase = Purchase::findOrFail($id);

        // Adjust product stock
        $product = Product::find($purchase->product_id);
        $product->stock -= $purchase->quantity;
        $product->save();

        $purchase->delete();
        return redirect()->route('purchases.index')->with('success', 'Compra eliminada correctamente.');
    }

    public function voucher(Purchase $purchase)
    {
        return view('tenant.purchases.voucher', compact('purchase'));
    }
}
