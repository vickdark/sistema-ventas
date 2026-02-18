<?php

namespace App\Http\Middleware\Tenant;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class CheckTenantPaymentStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        if (tenancy()->initialized) {
            $tenant = tenant();
            
            // Forzar actualización del estado del tenant para evitar problemas de caché
            $tenant->refresh();
            
            // Si el inquilino está al día (is_paid = true)
            if ($tenant->is_paid) {
                // Si intenta entrar a la página de "pago pendiente" estando al día, redirigir al dashboard
                if ($request->routeIs('tenant.payment-pending')) {
                    return redirect()->route('dashboard');
                }
                // Permitir acceso total al resto de rutas
                return $next($request);
            }

            // Si llegamos aquí, el inquilino NO está pagado (Suspendido/Vencido)
            // Bloquear acceso excepto a rutas permitidas

            // Rutas permitidas durante el bloqueo
            $allowedRoutes = [
                'tenant.payment-pending',
                'tenant.payment-notification.send',
                'login', // Mantenemos login en allowed para que no cause bucle, pero lo redirigimos abajo
                'logout'
            ];

            $currentRoute = $request->route() ? $request->route()->getName() : null;

            if ($currentRoute && !in_array($currentRoute, $allowedRoutes)) {
                // Si intenta acceder a cualquier otra ruta, redirigir a la página de pago pendiente
                return redirect()->route('tenant.payment-pending');
            }
            
            // Si está en el login, redirigir también a payment-pending para evitar confusión
            if ($currentRoute === 'login') {
                    return redirect()->route('tenant.payment-pending');
            }
        }

        return $next($request);
    }
}
