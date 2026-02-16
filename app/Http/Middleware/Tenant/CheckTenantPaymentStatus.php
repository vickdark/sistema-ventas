<?php

namespace App\Http\Middleware\Tenant;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantPaymentStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo aplicar si estamos dentro de un tenant
        if (tenancy()->initialized) {
            $tenant = tenant();
            
            // Si el inquilino no ha pagado (is_paid es falso)
            // Y no estamos ya en la ruta de pago pendiente o enviando la notificación
            if (!$tenant->is_paid && 
                !$request->routeIs('tenant.payment-pending') && 
                !$request->routeIs('tenant.payment-notification.send')) {
                return response()->view('tenant.payment-pending', [
                    'tenant' => $tenant
                ]);
            }
        }

        return $next($request);
    }
}
