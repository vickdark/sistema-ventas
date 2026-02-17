<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = Supplier::query();

            // Grid.js parameters
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('company', 'like', "%{$search}%")
                      ->orWhere('id', 'like', "%{$search}%");
                });
            }

            $total = $query->count();
            
            $suppliers = $query->orderBy('id', 'desc')
                               ->offset($offset)
                               ->limit($limit)
                               ->get();

            return response()->json([
                'data' => $suppliers,
                'total' => (int) $total,
                'status' => 'success'
            ]);
        }
        
        $config = [
            'routes' => [
                'index' => route('suppliers.index'),
                'edit' => route('suppliers.edit', ':id'),
                'destroy' => route('suppliers.destroy', ':id')
            ]
        ];

        return view('tenant.suppliers.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tenant.suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'suppliers' => 'required|array|min:1|max:5',
            'suppliers.*.name' => 'required|string|max:255',
            'suppliers.*.phone' => 'required|string|max:50|distinct',
            'suppliers.*.secondary_phone' => 'nullable|string|max:50',
            'suppliers.*.company' => 'required|string|max:255',
            'suppliers.*.email' => 'nullable|email|max:50',
            'suppliers.*.address' => 'required|string|max:255',
        ], [
            'suppliers.required' => 'Debe agregar al menos un proveedor.',
            'suppliers.max' => 'Solo puede registrar hasta 5 proveedores a la vez.',
            'suppliers.*.phone.distinct' => 'No puede haber teléfonos principales duplicados.',
        ]);

        $created = 0;
        $duplicates = [];

        foreach ($request->suppliers as $supplierData) {
            // Verificar si ya existe por teléfono principal
            if (Supplier::where('phone', $supplierData['phone'])->exists()) {
                $duplicates[] = $supplierData['phone'];
                continue;
            }

            Supplier::create($supplierData);
            $created++;
        }

        $message = "Se registraron {$created} proveedor(es) exitosamente.";
        
        if (count($duplicates) > 0) {
            $message .= " Los siguientes teléfonos ya existían: " . implode(', ', $duplicates);
        }

        return redirect()->route('suppliers.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('tenant.suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('tenant.suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'secondary_phone' => 'nullable|string|max:50',
            'company' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:50',
            'address' => 'required|string|max:255',
        ]);

        $supplier->update($request->all());
        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}
