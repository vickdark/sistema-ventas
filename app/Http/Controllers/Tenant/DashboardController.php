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
        try {
            /** @var \App\Models\Tenant\Usuario $user */
            $user = Auth::user();

            if (!$user || !$user->role) {
                return view('tenant.dashboards.generic', $this->getSubscriptionData());
            }

            $subscriptionData = $this->getSubscriptionData();

            // 1. Intentar por permiso específico
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

            // 2. Intentar por el slug del rol directamente
            $roleSlug = $user->role->slug;
            $roleViewName = "tenant.dashboards.{$roleSlug}";
            if (view()->exists($roleViewName)) {
                return view($roleViewName, $subscriptionData);
            }

            return view('tenant.dashboards.generic', $subscriptionData);
        } catch (\Exception $e) {
            \Log::error("Dashboard Error: " . $e->getMessage());
            return view('tenant.dashboards.generic', [
                'showSubscriptionStatus' => false,
                'badgeClass' => 'bg-secondary',
                'label' => 'ERROR',
                'serviceType' => null,
                'isMaintenance' => false,
                'auxLine' => 'Error al cargar datos del dashboard: ' . $e->getMessage(),
                'nextPaymentDate' => null,
                'formattedNextPaymentDate' => null
            ]);
        }
    }

    private function getSubscriptionData()
    {
        $data = [
            'serviceType' => null,
            'isMaintenance' => false,
            'badgeClass' => 'bg-success',
            'label' => 'ACTIVA',
            'auxLine' => null,
            'nextPaymentDate' => null,
            'formattedNextPaymentDate' => null,
            'showSubscriptionStatus' => false,
        ];

        try {
            $serviceType = tenant('service_type');
            $data['serviceType'] = $serviceType;
            $data['isMaintenance'] = ($serviceType === 'purchase');
            
            if (!in_array($serviceType, ['subscription', 'purchase'])) {
                return $data;
            }

            $next = tenant('next_payment_date');
            if (!$next) return $data;
            
            $data['nextPaymentDate'] = $next;
            $data['showSubscriptionStatus'] = true;

            $tz = tenant('timezone') ?? config('app.timezone', 'UTC');
            try {
                $now = \Carbon\Carbon::now($tz);
                $due = \Carbon\Carbon::parse($next, $tz);
                $data['formattedNextPaymentDate'] = $due->format('d/m/Y');
                
                if ($now->gt($due->endOfDay())) {
                    $data['badgeClass'] = 'bg-danger';
                    $data['label'] = 'VENCIDA';
                } elseif ($now->isSameDay($due)) {
                    $data['badgeClass'] = 'bg-warning';
                    $data['label'] = 'VENCE HOY';
                } else {
                    $days = (int)$now->diffInDays($due, false);
                    if ($days > 0) {
                        $data['label'] = "VENCE EN {$days}D";
                        if ($days <= 5) $data['badgeClass'] = 'bg-warning';
                    }
                }
            } catch (\Exception $dateErr) {
                $data['formattedNextPaymentDate'] = $next;
            }
        } catch (\Exception $e) {
            \Log::warning("Subscription data error: " . $e->getMessage());
        }

        return $data;
    }
}
