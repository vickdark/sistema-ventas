<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Role;
use App\Models\Tenant\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $roles = Role::withCount('users')->get();
            return response()->json([
                'data' => $roles,
                'status' => 'success'
            ]);
        }

        $config = [
            'routes' => [
                'index' => route('roles.index'),
                'create' => route('roles.create'),
                'edit' => route('roles.edit', ':id'),
                'destroy' => route('roles.destroy', ':id'),
                'permissions' => route('roles.edit_permissions', ':id'),
                'sync' => route('permissions.sync'),
            ],
            'permissions' => [
                'canCreate' => auth()->user()->hasPermission('roles.create'),
                'canEdit' => auth()->user()->hasPermission('roles.edit'),
                'canDestroy' => auth()->user()->hasPermission('roles.destroy'),
                'canSync' => auth()->user()->hasPermission('permissions.sync'),
            ],
            'tokens' => [
                'csrf' => csrf_token()
            ]
        ];

        return view('tenant.roles.index', compact('config'));
    }

    /**
     * Display the permissions cards for a specific role.
     */
    public function permissions(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('tenant.roles.role_permissions', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update permissions for a specific role.
     */
    public function updateRolePermissions(Request $request, Role $role)
    {
        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('roles.index')->with('success', "Permisos actualizados para el rol: {$role->nombre}");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tenant.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:roles,nombre|max:255',
            'descripcion' => 'nullable|max:500',
        ]);

        $role = Role::create([
            'nombre' => $request->nombre,
            'slug' => Str::slug($request->nombre),
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('roles.index')->with('success', 'Rol creado correctamente. Ahora puedes asignar sus permisos.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        return view('tenant.roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'nombre' => 'required|unique:roles,nombre,' . $role->id . '|max:255',
            'descripcion' => 'nullable|max:500',
        ]);

        $role->update([
            'nombre' => $request->nombre,
            'slug' => Str::slug($request->nombre),
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if ($role->slug === 'admin') {
            return redirect()->route('roles.index')->with('error', 'No se puede eliminar el rol de administrador por seguridad del sistema.');
        }

        // El sistema permite borrar el rol porque la base de datos pondrá el role_id en NULL automáticamente (nullOnDelete)
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente. Los usuarios que tenían este rol ahora no tienen ninguno asignado.');
    }
}
