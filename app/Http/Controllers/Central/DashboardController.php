<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Central\Tenant;
use Stancl\Tenancy\Database\Models\Domain;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTenants = Tenant::count();
        $totalDomains = Domain::count();
        $newTenantsThisMonth = Tenant::where('created_at', '>=', now()->startOfMonth())->count();
        
        // Contar usuarios totales en todos los inquilinos y recopilar métricas de DB
        $totalTenantUsers = 0;
        $tenantMetrics = [];
        
        Tenant::all()->each(function ($tenant) use (&$totalTenantUsers, &$tenantMetrics) {
            try {
                $metrics = $tenant->run(function () use (&$totalTenantUsers) {
                    $userCount = \App\Models\Tenant\Usuario::count();
                    $totalTenantUsers += $userCount;
                    
                    // Aseguramos que estamos usando la conexión 'tenant' que Stancl ya configuró
                    $dbConnection = DB::connection('tenant');
                    $dbName = $dbConnection->getDatabaseName();
                    
                    $tables = $dbConnection->select("SHOW TABLES");
                    $tableCount = count($tables);
                    
                    $sizeResult = $dbConnection->select("SELECT SUM(data_length + index_length) / 1024 / 1024 AS size FROM information_schema.TABLES WHERE table_schema = ?", [$dbName]);
                    $size = !empty($sizeResult) ? round($sizeResult[0]->size ?? 0, 2) : 0;

                    return [
                        'users' => $userCount,
                        'tables' => $tableCount,
                        'size' => $size,
                        'db_name' => $dbName
                    ];
                });
                
                $tenantMetrics[$tenant->id] = $metrics;
            } catch (\Throwable $e) {
                // Logueamos el error interno para depuración si es necesario
                \Illuminate\Support\Facades\Log::error("Error accediendo a datos del inquilino {$tenant->id}: " . $e->getMessage());
                
                $tenantMetrics[$tenant->id] = [
                    'users' => 0,
                    'tables' => 0,
                    'size' => 0,
                    'db_name' => 'Error: ' . substr($e->getMessage(), 0, 30) . '...'
                ];
            }
        });

        // Métricas HTTP (Central)
        $httpStats = [
            '2xx' => 0,
            '3xx' => 0,
            '4xx' => 0,
            '5xx' => 0,
        ];

        try {
            $httpStats = [
                '2xx' => \App\Models\Central\HttpLog::whereBetween('status', [200, 299])->count(),
                '3xx' => \App\Models\Central\HttpLog::whereBetween('status', [300, 399])->count(),
                '4xx' => \App\Models\Central\HttpLog::whereBetween('status', [400, 499])->count(),
                '5xx' => \App\Models\Central\HttpLog::whereBetween('status', [500, 599])->count(),
            ];
        } catch (\Throwable $e) {}

        $recentTenants = Tenant::with('domains')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Salud del servidor
        $serverHealth = [
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'memory_limit' => ini_get('memory_limit'),
            'php_version' => phpversion(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'db_connection' => 'Conectado', // Si llegamos aquí, la DB funciona
        ];

        $dbSize = 0;
        try {
            $centralDbName = config('database.connections.central.database');
            $results = DB::connection('central')->select("SELECT SUM(data_length + index_length) / 1024 / 1024 AS size FROM information_schema.TABLES WHERE table_schema = ?", [$centralDbName]);
            $dbSize = !empty($results) ? round($results[0]->size ?? 0, 2) : 0;
        } catch (\Throwable $e) {}


        return view('central.dashboard', compact(
            'totalTenants',
            'totalDomains',
            'newTenantsThisMonth',
            'recentTenants',
            'dbSize',
            'totalTenantUsers',
            'tenantMetrics',
            'httpStats',
            'serverHealth'
        ));
    }


}
