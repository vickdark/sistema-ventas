<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Product;
use App\Models\Tenant\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = Product::with('category');

            // Grid.js parameters
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('id', 'like', "%{$search}%");
                });
            }

            $total = $query->count();
            
            $products = $query->orderBy('id', 'desc')
                              ->offset($offset)
                              ->limit($limit)
                              ->get();

            return response()->json([
                'data' => $products,
                'total' => (int) $total,
                'status' => 'success'
            ]);
        }
        return view('tenant.products.index');
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
            'products' => 'required|array|min:1|max:5',
            'products.*.code' => 'required|string|max:255|distinct',
            'products.*.name' => 'required|string|max:255',
            'products.*.description' => 'nullable|string',
            'products.*.stock' => 'required|integer|min:0',
            'products.*.min_stock' => 'required|integer|min:0',
            'products.*.max_stock' => 'required|integer|min:0',
            'products.*.purchase_price' => 'required|numeric|min:0',
            'products.*.sale_price' => 'required|numeric|min:0',
            'products.*.entry_date' => 'required|date',
            'products.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'products.*.category_id' => 'required|exists:categories,id',
        ], [
            'products.required' => 'Debe agregar al menos un producto.',
            'products.max' => 'Solo puede registrar hasta 5 productos a la vez.',
            'products.*.code.distinct' => 'No puede haber códigos duplicados.',
            'products.*.image.image' => 'El archivo debe ser una imagen.',
            'products.*.image.max' => 'El tamaño máximo de la imagen es 2MB.',
        ]);

        $created = 0;
        $duplicates = [];

        foreach ($request->products as $index => $productData) {
            // Verificar si ya existe por código
            if (Product::where('code', $productData['code'])->exists()) {
                $duplicates[] = $productData['code'];
                continue;
            }

            // Procesar Imagen si existe
            if ($request->hasFile("products.{$index}.image")) {
                $path = $request->file("products.{$index}.image")->store('products', 'public');
                $productData['image'] = $path;
            }

            // Agregar el user_id
            $productData['user_id'] = auth()->id();

            Product::create($productData);
            $created++;
        }

        $message = "Se registraron {$created} producto(s) exitosamente.";
        
        if (count($duplicates) > 0) {
            $message .= " Los siguientes códigos ya existían: " . implode(', ', $duplicates);
        }

        return redirect()->route('products.index')->with('success', $message);
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
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
        ]);

        $data = $request->except('image_file');

        if ($request->hasFile('image_file')) {
            // Eliminar imagen anterior si existe
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image_file')->store('products', 'public');
        }

        $product->update($data);
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
