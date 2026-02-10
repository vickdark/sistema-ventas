<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Purchase;
use App\Models\Tenant\Product;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $purchases = Purchase::with('product')->get();
            return response()->json(['data' => $purchases]);
        }
        $purchases = Purchase::with('product')->get();
        return view('tenant.purchases.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        // $suppliers = Supplier::all(); // Uncomment when Supplier model is available
        return view('tenant.purchases.create', compact('products' /*, 'suppliers'*/));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            // 'supplier_id' => 'required|exists:suppliers,id', // Uncomment when Supplier model is available
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'voucher' => 'nullable|string|max:255',
        ]);

        $purchase = Purchase::create($request->all());
        return redirect()->route('purchases.index')->with('success', 'Purchase created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $purchase = Purchase::with('product')->findOrFail($id);
        return view('tenant.purchases.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $purchase = Purchase::with('product')->findOrFail($id);
        $products = Product::all();
        // $suppliers = Supplier::all(); // Uncomment when Supplier model is available
        return view('tenant.purchases.edit', compact('purchase', 'products' /*, 'suppliers'*/));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $purchase = Purchase::findOrFail($id);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            // 'supplier_id' => 'required|exists:suppliers,id', // Uncomment when Supplier model is available
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'voucher' => 'nullable|string|max:255',
        ]);

        $purchase->update($request->all());
        return redirect()->route('purchases.index')->with('success', 'Purchase updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->delete();
        return redirect()->route('purchases.index')->with('success', 'Purchase deleted successfully.');
    }
}
