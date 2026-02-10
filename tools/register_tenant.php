<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use Stancl\Tenancy\Database\Models\Domain;

// Nombre de la base de datos central (obtenido de la configuración de Laravel)
$centralDbName = config('database.connections.central.database');
// ID del inquilino
$tenantId = 'empresa_a';
// Nombre de la base de datos del inquilino según la nueva convención
$tenantDbName = $centralDbName . '_' . $tenantId;

$tenant = Tenant::find($tenantId);

if (!$tenant) {
    $tenant = Tenant::make([
        'id' => $tenantId,
    ]);
    $tenant->setInternal('db_name', $tenantDbName);
    $tenant->save();
    echo "Inquilino '{$tenantId}' creado con la base de datos '{$tenantDbName}'.\n";
} else {
    echo "Inquilino '{$tenantId}' ya existe. Actualizando el nombre de la base de datos.\n";
    $tenant->setInternal('db_name', $tenantDbName);
    $tenant->save();
    echo "Nombre de la base de datos del inquilino '{$tenantId}' actualizado a '{$tenantDbName}'.\n";
}

$baseDomain = parse_url(config('app.url'), PHP_URL_HOST) ?? 'sistema-ventas.test';
$domain = $tenant->domains()->firstOrCreate([
    'domain' => $tenantId . '.' . $baseDomain
]);

echo "Dominio '{$domain->domain}' asociado al inquilino '{$tenantId}'.\n";

// Opcional: Ejecutar migraciones para este inquilino inmediatamente después de crearlo
// Esto es útil si solo estás creando un inquilino a la vez.
// Para múltiples inquilinos, 'php artisan tenants:migrate' es más eficiente.
// tenancy()->for($tenant)->run(function () {
//     Artisan::call('migrate', ['--force' => true]);
//     echo "Migraciones ejecutadas para el inquilino '{$tenant->id}'.\n";
// });

echo "Proceso de registro de inquilino completado.\n";
