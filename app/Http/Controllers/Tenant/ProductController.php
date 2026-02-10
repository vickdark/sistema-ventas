<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Product;
use App\Models\Tenant\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with('category')->get();
            return response()->json(['data' => $products]);
        }
        $products = Product::with('category')->get();
        return view('tenant.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('tenant.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255|unique:products,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'max_stock' => 'required|integer|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'entry_date' => 'required|date',
            'image' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product = Product::create($request->all());
        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with('category')->findOrFail($id);
        return view('tenant.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::with('category')->findOrFail($id);
        $categories = Category::all();
        return view('tenant.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'code' => 'required|string|max:255|unique:products,code,' . $id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'max_stock' => 'required|integer|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'entry_date' => 'required|date',
            'image' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product->update($request->all());
        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
