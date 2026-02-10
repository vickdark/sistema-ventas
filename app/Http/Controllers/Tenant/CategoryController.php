<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = Category::query();

            // Grid.js parameters
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('id', 'like', "%{$search}%");
                });
            }

            $total = $query->count();
            
            $categories = $query->orderBy('id', 'desc')
                               ->offset($offset)
                               ->limit($limit)
                               ->get();

            return response()->json([
                'data' => $categories,
                'total' => (int) $total,
                'status' => 'success'
            ]);
        }
        return view('tenant.categories.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tenant.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'categories' => 'required|array|min:1',
            'categories.*' => 'required|string|max:255|distinct',
        ], [
            'categories.required' => 'Debe agregar al menos una categoría.',
            'categories.*.required' => 'El nombre de la categoría es obligatorio.',
            'categories.*.distinct' => 'No puede haber categorías duplicadas.',
        ]);

        $created = 0;
        $duplicates = [];

        foreach ($request->categories as $categoryName) {
            $categoryName = trim($categoryName);
            
            // Verificar si ya existe
            if (Category::where('name', $categoryName)->exists()) {
                $duplicates[] = $categoryName;
                continue;
            }

            Category::create(['name' => $categoryName]);
            $created++;
        }

        $message = "Se crearon {$created} categoría(s) exitosamente.";
        
        if (count($duplicates) > 0) {
            $message .= " Las siguientes ya existían: " . implode(', ', $duplicates);
        }

        return redirect()->route('categories.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);
        return view('tenant.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        return view('tenant.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
        ]);

        $category->update($request->all());
        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
