<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');

            $query = ExpenseCategory::query();

            if ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            }

            $total = (int) $query->count();
            $categories = $query->orderBy('name', 'asc')
                                ->offset($offset)
                                ->limit($limit)
                                ->get();
 
            return response()->json([
                'data' => $categories,
                'total' => $total,
                'status' => 'success'
            ]);
         }

        $config = [
            'routes' => [
                'index' => route('expense-categories.index'),
                'create' => route('expense-categories.create'),
                'edit' => route('expense-categories.edit', ':id'),
                'destroy' => route('expense-categories.destroy', ':id')
            ]
        ];

        return view('tenant.expenses.categories.index', compact('config'));
    }

    public function create()
    {
        return view('tenant.expenses.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:20',
        ]);

        ExpenseCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color ?? '#6c757d',
            'is_active' => true
        ]);

        return redirect()->route('expense-categories.index')
                         ->with('success', 'Categoría registrada correctamente');
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        return view('tenant.expenses.categories.edit', compact('expenseCategory'));
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:20',
        ]);

        $expenseCategory->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
        ]);

        return redirect()->route('expense-categories.index')
                         ->with('success', 'Categoría actualizada correctamente');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->expenses()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la categoría porque tiene gastos asociados.'
            ], 422);
        }

        $expenseCategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Categoría eliminada correctamente'
        ]);
    }
}
