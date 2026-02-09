<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

$tenantId = 'empresa_a';
/** @var \App\Models\Tenant $tenant */
$tenant = Tenant::find($tenantId);

if ($tenant) {
    // Asegurarse de que el inquilino tenga el nombre de la base de datos configurado internamente
    if (!$tenant->getInternal('db_name')) {
        $centralDbName = 'usuariosmultitenancy';
        $tenantDbName = $centralDbName . '_' . $tenantId;
        $tenant->setInternal('db_name', $tenantDbName);
    }

    echo "--- Tenant Details ---\n";
    echo "ID: " . $tenant->id . "\n";
    echo "Attributes:\n";
    print_r($tenant->getAttributes());
    
echo "\n--- Tenancy Database Config ---\n";
try {
    echo "Internal db_name: " . $tenant->getInternal('db_name') . "\n";
} catch (\Exception $e) {
    echo "Could not get internal db_name: " . $e->getMessage() . "\n";
}

if ($tenant instanceof \Stancl\Tenancy\Contracts\TenantWithDatabase) {
    echo "Tenant implements TenantWithDatabase: YES\n";
    echo "Database Name: " . $tenant->database()->getName() . "\n";
} else {
    echo "Tenant implements TenantWithDatabase: NO\n";
}

echo "\n--- Current Connection Status ---\n";
echo "Default connection: " . config('database.default') . "\n";

$tenant->run(function() use ($tenantId, $tenant) {
    echo "\n--- Inside Tenant Context ($tenantId) ---\n";
    echo "Current default connection: " . config('database.default') . "\n";
    echo "Current DB name: " . DB::connection()->getDatabaseName() . "\n";
    
    $tenantConn = config('database.connections.tenant');
    echo "Tenant connection config (database): " . ($tenantConn['database'] ?? 'NULL') . "\n";
});
} else {
    echo "Tenant '$tenantId' not found.\n";
}
