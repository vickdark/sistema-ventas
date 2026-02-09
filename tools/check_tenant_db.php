<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tenantId = 'empresa_a';

/** @var \App\Models\Tenant $tenant */
$tenant = Tenant::find($tenantId);

if (!$tenant) {
    echo "Error: Inquilino '{$tenantId}' no encontrado.\n";
    exit(1);
}

echo "Verificando conexión para el inquilino '{$tenantId}'...\n";

// Asegurarse de que el inquilino tenga el nombre de la base de datos configurado internamente
if (!$tenant->getInternal('db_name')) {
    $centralDbName = 'usuariosmultitenancy';
    $tenantDbName = $centralDbName . '_' . $tenantId;
    $tenant->setInternal('db_name', $tenantDbName);
}

$tenant->run(function () use ($tenantId) {
    $dbName = DB::connection()->getDatabaseName();
    echo "Conectado a la base de datos del inquilino: '{$dbName}'\n";

    try {
        $tables = Schema::getTableListing();
        echo "Tablas encontradas en '{$dbName}': " . count($tables) . "\n";
        
        // Mostrar algunas tablas para verificar
        foreach (array_slice($tables, 0, 10) as $table) {
            echo "- " . $table . "\n";
        }
    } catch (\Exception $e) {
        echo "Error al listar tablas: " . $e->getMessage() . "\n";
    }
});

echo "Verificación completada.\n";
