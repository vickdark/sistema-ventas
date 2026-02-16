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

            $tz = tenant('timezone') ?? config('app.timezone', 'UTC');
            $raw = tenant('next_payment_date');
            $due = null;
            if ($raw) {
                if (Carbon::hasFormat($raw, 'Y-m-d')) {
                    $due = Carbon::createFromFormat('Y-m-d', $raw, $tz);
                } elseif (Carbon::hasFormat($raw, 'd/m/Y')) {
                    $due = Carbon::createFromFormat('d/m/Y', $raw, $tz);
                } elseif (Carbon::hasFormat($raw, 'd-m-Y')) {
                    $due = Carbon::createFromFormat('d-m-Y', $raw, $tz);
                } else {
                    $due = Carbon::parse($raw, $tz);
                }
                $due = $due->startOfDay();
            }

            $today = Carbon::now($tz)->startOfDay();

            $shouldBlock = false;
            if (!$tenant->is_paid) {
                if ($due === null) {
                    $shouldBlock = true;
                } else {
                    // Bloquear solo si HOY es posterior al día de vencimiento (vence hoy permite acceso)
                    $shouldBlock = $today->gt($due);
                }
            }

            if (
                $shouldBlock &&
                !$request->routeIs('login') &&
                !$request->routeIs('tenant.payment-notification.send') &&
                !$request->routeIs('tenant.payment-pending')
            ) {
                return redirect()->route('login')->with('payment_pending', true);
            }
        }

        return $next($request);
    }
}
