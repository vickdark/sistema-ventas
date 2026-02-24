<?php

namespace App\Http\Middleware\Tenant;

use App\Models\Tenant\EcommerceConfiguration;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEcommerceActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ignorar si es una ruta administrativa del tenant (dashboard, login, etc.)
        if ($request->is('login') || $request->is('dashboard*') || $request->is('payment*')) {
            return $next($request);
        }

        try {
            $config = EcommerceConfiguration::first();

            // Si no existe configuración o is_active es false
            if (!$config || !$config->is_active) {
                // Si el usuario es administrador autenticado, permitir acceso para previsualizar
                if (auth()->check() && auth()->user()->hasRole('admin')) {
                    return $next($request);
                }
                
                return response()->view('tenant.ecommerce.maintenance', compact('config'));
            }
        } catch (\Exception $e) {
            // En caso de error (tabla no existe, etc), permitir continuar o mostrar error genérico
            \Log::error('Error checking ecommerce status: ' . $e->getMessage());
        }

        return $next($request);
    }
}
