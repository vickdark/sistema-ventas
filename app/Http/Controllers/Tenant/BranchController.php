<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = \App\Models\Tenant\Branch::query();

            // Grid.js parameters
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('address', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $total = $query->count();
            
            $branches = $query->orderBy('id', 'desc')
                               ->offset($offset)
                               ->limit($limit)
                               ->get();

            return response()->json([
                'data' => $branches,
                'total' => (int) $total,
                'status' => 'success'
            ]);
        }
        
        $config = [
            'routes' => [
                'index' => route('branches.index'),
                'edit' => route('branches.edit', ':id'),
                'destroy' => route('branches.destroy', ':id')
            ]
        ];

        return view('tenant.branches.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tenant.branches.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_main' => 'nullable|boolean',
        ]);

        $data = $request->all();
        
        // Si se marca como principal, quitar el flag a las demás
        if ($request->has('is_main') && $request->is_main) {
            \App\Models\Tenant\Branch::where('is_main', true)->update(['is_main' => false]);
        }

        \App\Models\Tenant\Branch::create($data);

        return redirect()->route('branches.index')->with('success', 'Sucursal creada exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $branch = \App\Models\Tenant\Branch::findOrFail($id);
        return view('tenant.branches.edit', compact('branch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $branch = \App\Models\Tenant\Branch::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_main' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_main'] = $request->has('is_main');
        $data['is_active'] = $request->has('is_active');

        // Si se marca como principal, quitar el flag a las demás
        if ($data['is_main']) {
            \App\Models\Tenant\Branch::where('is_main', true)->where('id', '!=', $id)->update(['is_main' => false]);
        }

        $branch->update($data);

        return redirect()->route('branches.index')->with('success', 'Sucursal actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $branch = \App\Models\Tenant\Branch::findOrFail($id);
        
        if ($branch->is_main) {
            return redirect()->route('branches.index')->with('error', 'No se puede eliminar la sucursal principal.');
        }

        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'Sucursal eliminada exitosamente.');
    }

    /**
     * Set the active branch for the current session.
     */
    public function setActive(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id'
        ]);

        $user = auth()->user();
        
        // Si no es admin, verificar que tenga permiso para esa sucursal
        if (!$user->isAdmin() && $user->branch_id != $request->branch_id) {
            return back()->with('error', 'No tienes permiso para acceder a esta sucursal.');
        }

        session(['active_branch_id' => $request->branch_id]);

        return back()->with('success', 'Sucursal cambiada correctamente.');
    }
}
