<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Expense;
use App\Models\Tenant\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');

            $query = Expense::with(['category', 'user', 'branch']);

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('reference', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('category', function($q2) use ($search) {
                          $q2->where('name', 'like', "%{$search}%");
                      });
                });
            }

            $total = (int) $query->count();
            $expenses = $query->orderBy('date', 'desc')
                              ->orderBy('created_at', 'desc')
                              ->offset($offset)
                              ->limit($limit)
                              ->get();

            return response()->json([
                'data' => $expenses,
                'total' => $total,
                'status' => 'success'
            ]);
        }

        $config = [
            'routes' => [
                'index' => route('expenses.index'),
                'create' => route('expenses.create'),
                'show' => route('expenses.show', ':id'),
                'destroy' => route('expenses.destroy', ':id')
            ]
        ];

        return view('tenant.expenses.index', compact('config'));
    }

    public function show(Expense $expense)
    {
        $expense->load(['category', 'user', 'branch']);
        return view('tenant.expenses.show', compact('expense'));
    }

    public function create()
    {
        $categories = ExpenseCategory::all();
        // $branches = auth()->user()->branches; // Si multisede
        return view('tenant.expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Expense::create([
            'expense_category_id' => $request->expense_category_id,
            'user_id' => auth()->id(),
            'branch_id' => auth()->user()->branch_id, // Asumiendo que el usuario tiene branch_id
            'name' => $request->name,
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description,
            'reference' => $request->reference,
        ]);

        return redirect()->route('expenses.index')
                         ->with('success', 'Gasto registrado correctamente');
    }
}
