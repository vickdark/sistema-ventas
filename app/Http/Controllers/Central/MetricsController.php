<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\HttpLog;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MetricsController extends Controller
{
    public function index(Request $request)
    {
        // Paginación de logs HTTP
        $logs = HttpLog::orderBy('created_at', 'desc')
            ->when($request->tenant_id, function($query, $tenantId) {
                return $query->where('tenant_id', $tenantId);
            })
            ->when($request->status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->paginate(50)
            ->withQueryString();

        // Métricas de Tenants
        $tenants = Tenant::all();
        $tenantMetrics = [];
        foreach ($tenants as $tenant) {
            $tenantMetrics[$tenant->id] = $tenant->run(function () {
                $dbName = config('database.connections.tenant.database');
                $sizeResult = DB::select("SELECT SUM(data_length + index_length) / 1024 / 1024 AS size FROM information_schema.TABLES WHERE table_schema = ?", [$dbName]);
                return [
                    'db_name' => $dbName,
                    'size' => round($sizeResult[0]->size ?? 0, 2),
                    'users' => \App\Models\Usuarios\Usuario::count(),
                    'sales' => DB::table('sales')->count(),
                ];
            });
        }

        // Estadísticas generales para gráficos
        $stats = [
            'total_requests' => HttpLog::count(),
            'avg_duration' => round(HttpLog::avg('duration') ?? 0, 4),
            'status_distribution' => HttpLog::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get(),
            'methods' => HttpLog::select('method', DB::raw('count(*) as count'))
                ->groupBy('method')
                ->get(),
        ];

        return view('central.metrics.index', compact('logs', 'tenantMetrics', 'stats', 'tenants'));
    }

    public function clearLogs()
    {
        HttpLog::truncate();
        return back()->with('status', 'Historial de logs HTTP limpiado correctamente.');
    }
}
