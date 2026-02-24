<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Usuario;
use App\Models\Tenant\Role;
use App\Models\Tenant\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');
            $sort = $request->get('sort', 'id');
            $order = $request->get('order', 'desc');

            $query = Usuario::with('role');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $total = $query->count();
            
            $usuarios = $query->orderBy($sort, $order)
                              ->offset($offset)
                              ->limit($limit)
                              ->get();

            return response()->json([
                'data' => $usuarios,
                'total' => $total,
                'totalNotFiltered' => Usuario::count()
            ]);
        }
        
        $config = [
            'routes' => [
                'index' => route('usuarios.index'),
                'create' => route('usuarios.create'),
                'store' => route('usuarios.store'),
                'edit' => route('usuarios.edit', ':id'),
                'update' => route('usuarios.update', ':id'),
                'destroy' => route('usuarios.destroy', ':id'),
                'toggle_status' => route('usuarios.toggle-status', ':id')
            ]
        ];
        
        return view('tenant.usuarios.index', compact('config'));
    }

    /**
     * Alternar el estado activo/inactivo del usuario.
     */
    public function toggleStatus(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);
        
        $usuario->update([
            'is_active' => !$usuario->is_active
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado del usuario actualizado correctamente',
            'is_active' => $usuario->is_active
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        $branches = Branch::where('is_active', true)->get();
        return view('tenant.usuarios.create', compact('roles', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
            'branch_id' => [
                function ($attribute, $value, $fail) use ($request) {
                    $role = Role::find($request->role_id);
                    if ($role && $role->slug !== 'admin' && $role->slug !== 'super-admin' && empty($value)) {
                        $fail('La sucursal es obligatoria para este rol.');
                    }
                },
                'nullable',
                'exists:branches,id',
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 
                'string', 
                'lowercase', 
                'email', 
                'max:255', 
                Rule::unique('users')
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $role = Role::find($request->role_id);
        $branchId = ($role->slug === 'admin' || $role->nombre === 'Administrador') ? null : $request->branch_id;

        Usuario::create([
            'role_id' => $request->role_id,
            'branch_id' => $branchId,
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $usuario = Usuario::findOrFail($id);
        return view('tenant.usuarios.show', compact('usuario'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $usuario = Usuario::findOrFail($id);
        $roles = Role::all();
        $branches = Branch::where('is_active', true)->get();
        return view('tenant.usuarios.edit', compact('usuario', 'roles', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);
        $rules = [
            'role_id' => ['required', 'exists:roles,id'],
            'branch_id' => [
                function ($attribute, $value, $fail) use ($request) {
                    $role = Role::find($request->role_id);
                    if ($role && $role->slug !== 'admin' && $role->slug !== 'super-admin' && empty($value)) {
                        $fail('La sucursal es obligatoria para este rol.');
                    }
                },
                'nullable',
                'exists:branches,id',
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 
                'string', 
                'lowercase', 
                'email', 
                'max:255', 
                Rule::unique('users')->ignore($usuario->id)
            ],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Rules\Password::defaults()];
        }

        $request->validate($rules);

        $role = Role::find($request->role_id);
        $branchId = ($role->slug === 'admin' || $role->nombre === 'Administrador') ? null : $request->branch_id;

        $data = [
            'role_id' => $request->role_id,
            'branch_id' => $branchId,
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);

        if ($usuario->id == Auth::id()) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['message' => 'No puedes eliminarte a ti mismo.'], 403);
            }
            return redirect()->route('usuarios.index')
                ->with('error', 'No puedes eliminarte a ti mismo.');
        }

        $usuario->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['message' => 'Usuario eliminado correctamente.']);
        }

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
