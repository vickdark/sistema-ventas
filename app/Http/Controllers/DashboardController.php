<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Redirige al usuario a su dashboard específico basado en permisos.
     */
    public function index()
    {
        /** @var \App\Models\Usuarios\Usuario \$user */
        $user = Auth::user();

        // Validamos que el usuario y su rol existan para evitar errores
        if (!$user || !$user->role) {
            return view('dashboard');
        }

        // 1. Intentar por permiso específico (ej: dashboard.admin, dashboard.vendedor)
        $roleDashboard = $user->role->permissions()
            ->where('slug', 'like', 'dashboard.%')
            ->where('slug', '!=', 'dashboard')
            ->first();

        if ($roleDashboard) {
            $viewName = str_replace('dashboard.', 'dashboards.', $roleDashboard->slug);
            if (view()->exists($viewName)) {
                return view($viewName);
            }
        }

        // 2. Intentar por el slug del rol directamente (ej: rol 'admin' -> dashboards.admin)
        $roleSlug = $user->role->slug;
        $roleViewName = "dashboards.{$roleSlug}";
        if (view()->exists($roleViewName)) {
            return view($roleViewName);
        }

        // 3. Dashboard genérico por defecto
        return view('dashboard');
    }
}
