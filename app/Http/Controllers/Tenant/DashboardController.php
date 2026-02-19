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

            if (!$user) {
                return redirect()->route('login');
            }

            $subscriptionData = $this->getSubscriptionData();

            // 1. Si es admin, mostramos admin (Ruta fija para evitar fallos de mayúsculas)
            if ($user->isAdmin()) {
                return view('tenant.dashboards.admin', $subscriptionData);
            }

            // 2. Si es vendedor, mostramos vendedor
            if ($user->hasRole('vendedor') || $user->hasRole('Vendedor')) {
                return view('tenant.dashboards.vendedor', $subscriptionData);
            }

            // 3. Si no coincide nada o no tiene rol, el genérico
            return view('tenant.dashboards.generic', $subscriptionData);

        } catch (\Throwable $e) {
            \Log::error("Error Fatal en Dashboard: " . $e->getMessage());
            // En producción, al menos mostramos el genérico si el específico falla
            return view('tenant.dashboards.generic', [
                'showSubscriptionStatus' => false,
                'businessName' => config('app.name'),
                'label' => 'ERROR'
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
