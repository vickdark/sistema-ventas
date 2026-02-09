<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

$tenantId = 'empresa_a';
/** @var \App\Models\Tenant $tenant */
$tenant = Tenant::find($tenantId);

if (!$tenant) {
    echo "Error: Inquilino '{$tenantId}' no encontrado.\n";
    exit(1);
}

// Asegurarse de que el inquilino tenga el nombre de la base de datos configurado internamente
if (!$tenant->getInternal('db_name')) {
    $centralDbName = 'usuariosmultitenancy';
    $tenantDbName = $centralDbName . '_' . $tenantId;
    $tenant->setInternal('db_name', $tenantDbName);
}

echo "Cambiando al contexto del inquilino '{$tenantId}'...\n";

$tenant->run(function () use ($tenantId) {
    $dbName = DB::connection()->getDatabaseName();
    echo "Conectado a la base de datos: '{$dbName}'\n";
    
    echo "Ejecutando migraciones para el inquilino '{$tenantId}'...\n";
    Artisan::call('migrate', [
        '--path' => 'database/migrations/tenant',
        '--force' => true,
    ]);
    echo Artisan::output();

    echo "Ejecutando seeders para el inquilino '{$tenantId}'...\n";
    Artisan::call('db:seed', [
        '--force' => true,
    ]);
    echo Artisan::output();
    
    echo "Proceso completado para el inquilino '{$tenantId}'.\n";
});

echo "Proceso de migración de inquilino completado.\n";
