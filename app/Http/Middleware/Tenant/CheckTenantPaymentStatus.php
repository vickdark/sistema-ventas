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
        try {
            if (tenancy()->initialized) {
                $tenant = tenant();
                
                // Si el inquilino está al día (is_paid = true)
                if ($tenant && $tenant->is_paid) {
                    if ($request->routeIs('tenant.payment-pending')) {
                        return redirect()->route('dashboard');
                    }
                    return $next($request);
                }

                // Si llegamos aquí, el inquilino NO está pagado
                $allowedRoutes = [
                    'tenant.payment-pending',
                    'tenant.payment-notification.send',
                    'login', 
                    'logout'
                ];

                $currentRoute = $request->route() ? $request->route()->getName() : null;

                if ($currentRoute && !in_array($currentRoute, $allowedRoutes)) {
                    return redirect()->route('tenant.payment-pending');
                }
                
                if ($currentRoute === 'login') {
                    return redirect()->route('tenant.payment-pending');
                }
            }
        } catch (\Exception $e) {
            \Log::error("Payment Status Middleware Error: " . $e->getMessage());
        }

        return $next($request);
    }
}
