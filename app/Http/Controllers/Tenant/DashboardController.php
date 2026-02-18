<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Redirige al usuario a su dashboard específico basado en permisos.
     */
    public function index()
    {
        /** @var \App\Models\Tenant\Usuario $user */
        $user = Auth::user();

        // Validamos que el usuario y su rol existan para evitar errores
        if (!$user || !$user->role) {
            return view('tenant.dashboards.generic');
        }

        $subscriptionData = $this->getSubscriptionData();

        // 1. Intentar por permiso específico (ej: dashboard.admin, dashboard.vendedor)
        $roleDashboard = $user->role->permissions()
            ->where('slug', 'like', 'dashboard.%')
            ->where('slug', '!=', 'dashboard')
            ->first();

        if ($roleDashboard) {
            $viewName = str_replace('dashboard.', 'tenant.dashboards.', $roleDashboard->slug);
            if (view()->exists($viewName)) {
                return view($viewName, $subscriptionData);
            }
        }

        // 2. Intentar por el slug del rol directamente (ej: rol 'admin' -> dashboards.admin)
        $roleSlug = $user->role->slug;
        $roleViewName = "tenant.dashboards.{$roleSlug}";
        if (view()->exists($roleViewName)) {
            return view($roleViewName, $subscriptionData);
        }

        // 3. Dashboard genérico por defecto
        return view('tenant.dashboards.generic', $subscriptionData);
    }

    private function getSubscriptionData()
    {
        $serviceType = tenant('service_type');
        $isMaintenance = ($serviceType === 'purchase');
        
        // Default values
        $data = [
            'serviceType' => $serviceType,
            'isMaintenance' => $isMaintenance,
            'badgeClass' => 'bg-success',
            'label' => 'ACTIVA',
            'auxLine' => null,
            'nextPaymentDate' => null,
            'formattedNextPaymentDate' => null,
            'showSubscriptionStatus' => false,
        ];

        if (!in_array($serviceType, ['subscription', 'purchase'])) {
            return $data;
        }

        $data['showSubscriptionStatus'] = true;
        $next = tenant('next_payment_date');
        $data['nextPaymentDate'] = $next;
        
        if (!$next) {
            return $data;
        }

        $tz = tenant('timezone') ?? config('app.timezone', 'UTC');
        $data['formattedNextPaymentDate'] = \Carbon\Carbon::parse($next, $tz)->format('d/m/Y');

        $now = \Carbon\Carbon::now($tz);
        $dueStart = \Carbon\Carbon::parse($next, $tz)->startOfDay();
        $dueEnd = \Carbon\Carbon::parse($next, $tz)->endOfDay();
        $todayStart = $now->copy()->startOfDay();

        if ($now->gt($dueEnd)) {
            $data['badgeClass'] = 'bg-danger';
            $data['label'] = 'VENCIDA';
        } elseif ($todayStart->equalTo($dueStart)) {
            $data['badgeClass'] = 'bg-warning';
            $data['label'] = 'VENCE HOY';
        } else {
            $days = $todayStart->diffInDays($dueStart, false);
            if ($days > 0) {
                if ($isMaintenance) {
                    // Para purchase/maintenance solo mostramos mensaje si faltan 5 días o menos
                    if ($days <= 5) {
                        $data['badgeClass'] = 'bg-warning';
                        $daysText = ($days === 1) ? '1 día' : "$days días";
                        $data['auxLine'] = "Faltan $daysText para el pago de mantenimiento.";
                        $data['label'] = "VENCE EN " . strtoupper($daysText);
                    } else {
                        // Si faltan más de 5 días, ocultamos la tarjeta para tipo purchase
                        $data['showSubscriptionStatus'] = false;
                    }
                } else {
                    // Para suscripción normal
                    if ($days <= 5) { 
                        $data['badgeClass'] = 'bg-warning';
                    }
                    $data['label'] = 'VENCE EN ' . $days . 'D';
                }
            }
        }
        
        return $data;
    }
}
