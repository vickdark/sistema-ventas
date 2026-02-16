<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class SyncPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:sync {--clean : Elimina permisos que ya no existen en las rutas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza las rutas del sistema con la tabla de permisos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando sincronización de permisos...');

        $routes = Route::getRoutes();
        $permissionsCreated = 0;
        $activeSlugs = [];

        foreach ($routes as $route) {
            $name = $route->getName();
            
            // Solo procesamos rutas que tengan nombre y no sean de sistema/ignorar
            if ($name && $this->shouldSync($name)) {
                $activeSlugs[] = $name;
                
                $permission = \App\Models\Tenant\Permission::updateOrCreate(
                    ['slug' => $name],
                    [
                        'nombre' => $this->generateName($name),
                        'descripcion' => $this->generateDescription($name, $route),
                        'is_menu' => $this->isMenu($name),
                        'icon' => $this->generateIcon($name),
                        'module' => $this->generateModuleName($name),
                        'order' => $this->generateOrder($name),
                    ]
                );

                if ($permission->wasRecentlyCreated) {
                    $permissionsCreated++;
                    $this->line(" <info>✔</info> Nuevo permiso detectado: {$name} -> {$permission->nombre}");
                }
            }
        }

        // Limpieza automática de permisos antiguos y excluidos
        $excludedPrefixes = [
            'sanctum.', 'ignition.', 'livewire.', 'verification.', 
            'password.', 'login', 'logout', 'register',
            'profile.', 'storage.', 'central.', 'stancl.',
            'tenant.payment-notification.', 'tenant.payment-pending',
            'tenant.profile.password.update'
        ];

        // 1. Eliminar permisos que coinciden con los prefijos excluidos
        $deletedExcluded = \App\Models\Tenant\Permission::where(function($q) use ($excludedPrefixes) {
            foreach ($excludedPrefixes as $prefix) {
                $q->orWhere('slug', 'like', $prefix . '%');
            }
        })->delete();

        if ($deletedExcluded > 0) {
            $this->warn("Se eliminaron {$deletedExcluded} permisos de rutas excluidas.");
        }

        // 2. Limpieza de rutas que ya no existen (SOLO si se pasa el flag --clean)
        if ($this->option('clean')) {
            $deletedOld = \App\Models\Tenant\Permission::whereNotIn('slug', $activeSlugs)
                ->whereNotIn('slug', [
                    'dashboard', 'roles.index', 'roles.create', 'roles.edit', 'roles.destroy',
                    'permissions.index', 'permissions.create', 'permissions.edit', 'permissions.destroy', 'permissions.sync'
                ])
                ->delete();
            
            if ($deletedOld > 0) {
                $this->warn("Se eliminaron {$deletedOld} permisos de rutas que ya no existen.");
            }
        }

        $this->info("Sincronización completada. Total nuevos: {$permissionsCreated}.");
    }

    /**
     * Determina si una ruta debe ser sincronizada.
     */
    protected function shouldSync($name)
    {
        $excludedPrefixes = [
            'sanctum.', 'ignition.', 'livewire.', 'verification.', 
            'password.', 'login', 'logout', 'register',
            'profile.', 'storage.', 'central.', 'stancl.',
            'tenant.payment-notification.', 'tenant.payment-pending',
            'tenant.profile.password.update'
        ];
        
        foreach ($excludedPrefixes as $prefix) {
            if (str_starts_with($name, $prefix)) {
                return false;
            }
        }

        // Permitir dashboard y rutas con puntos
        if ($name === 'dashboard' || str_contains($name, '.')) {
            return true;
        }

        return false;
    }

    /**
     * Genera un nombre legible y en español a partir del slug.
     */
    protected function generateName($slug)
    {
        if ($slug === 'dashboard') return "Tablero Principal";
        
        if (str_starts_with($slug, 'dashboard.')) {
            $role = ucfirst(str_replace('dashboard.', '', $slug));
            return "Dashboard {$role}";
        }

        $parts = explode('.', $slug);
        $action = end($parts);
        $entitySlug = count($parts) > 1 ? $parts[count($parts) - 2] : 'General';

        $translations = $this->getTranslations();
        $entities = $this->getEntityNames();

        $actionName = $translations[$action] ?? ucfirst($action);
        $entityName = $entities[$entitySlug]['plural'] ?? ucfirst($entitySlug);

        // Especial para el menú (index)
        if ($action === 'index') {
            return $entityName;
        }

        return "{$actionName} " . ($entities[$entitySlug]['singular'] ?? $entitySlug);
    }

    /**
     * Nombres de las entidades en español (singular y plural)
     */
    protected function getEntityNames()
    {
        return [
            'usuarios'       => ['singular' => 'Usuario', 'plural' => 'Usuarios'],
            'roles'          => ['singular' => 'Rol', 'plural' => 'Roles'],
            'permissions'    => ['singular' => 'Permiso', 'plural' => 'Permisos'],
            'clients'        => ['singular' => 'Cliente', 'plural' => 'Clientes'],
            'products'       => ['singular' => 'Producto', 'plural' => 'Productos'],
            'categories'     => ['singular' => 'Categoría', 'plural' => 'Categorías'],
            'purchases'      => ['singular' => 'Compra', 'plural' => 'Compras'],
            'suppliers'      => ['singular' => 'Proveedor', 'plural' => 'Proveedores'],
            'sales'          => ['singular' => 'Venta', 'plural' => 'Ventas'],
            'cash-registers' => ['singular' => 'Caja', 'plural' => 'Cajas'],
            'abonos'         => ['singular' => 'Abono', 'plural' => 'Abonos'],
            'reports'        => ['singular' => 'Reporte', 'plural' => 'Reportes'],
            'config'         => ['singular' => 'Configuración', 'plural' => 'Configuraciones'],
            'import'         => ['singular' => 'Importación', 'plural' => 'Importación Masiva'],
        ];
    }

    /**
     * Obtiene las traducciones de las acciones comunes.
     */
    protected function getTranslations()
    {
        return [
            'index'   => 'Ver',
            'show'    => 'Ver Detalle',
            'create'  => 'Crear',
            'store'   => 'Guardar',
            'edit'    => 'Editar',
            'update'  => 'Actualizar',
            'destroy' => 'Eliminar',
            'sync'    => 'Sincronizar',
            'export'  => 'Exportar',
            'import'  => 'Importar',
            'close'   => 'Cerrar',
            'close-form' => 'Cierre',
        ];
    }

    /**
     * Genera una descripción clara basada en la ruta y el controlador.
     */
    protected function generateDescription($slug, $route)
    {
        if ($slug === 'dashboard') return "Acceso al tablero principal de estadísticas";
        
        if (str_starts_with($slug, 'dashboard.')) {
            $role = str_replace('dashboard.', '', $slug);
            return "Vista de panel principal personalizada para el rol {$role}";
        }

        $parts = explode('.', $slug);
        $action = end($parts);
        $entitySlug = count($parts) > 1 ? $parts[count($parts) - 2] : 'General';
        
        $translations = $this->getTranslations();
        $entities = $this->getEntityNames();
        
        $actionName = $translations[$action] ?? ucfirst($action);
        $entityName = $entities[$entitySlug]['singular'] ?? $entitySlug;

        return "Permite {$actionName} {$entityName} en el sistema";
    }

    /**
     * Determina si la ruta debe aparecer en el menú.
     */
    protected function isMenu($slug)
    {
        if ($slug === 'dashboard') return true;

        // No mostrar la gestión de permisos en el menú lateral directamente
        if (str_starts_with($slug, 'permissions.')) return false;
        
        $parts = explode('.', $slug);
        $action = end($parts);

        // Solo el index suele ir al menú, a menos que sea algo muy específico
        if ($action !== 'index') {
            return false;
        }

        return true;
    }

    /**
     * Genera un icono sugerido según el nombre del módulo o entidad.
     */
    protected function generateIcon($slug)
    {
        if ($slug === 'dashboard') return 'fa-solid fa-gauge-high';

        $parts = explode('.', $slug);
        $entity = count($parts) > 1 ? $parts[count($parts) - 2] : 'General';

        $icons = [
            'usuarios'       => 'fa-solid fa-users',
            'roles'          => 'fa-solid fa-user-shield',
            'permissions'    => 'fa-solid fa-key',
            'inventario'     => 'fa-solid fa-boxes-stacked',
            'products'       => 'fa-solid fa-box',
            'categories'     => 'fa-solid fa-tags',
            'purchases'      => 'fa-solid fa-cart-shopping',
            'suppliers'      => 'fa-solid fa-truck',
            'sales'          => 'fa-solid fa-cash-register',
            'cash-registers' => 'fa-solid fa-vault',
            'abonos'         => 'fa-solid fa-hand-holding-dollar',
            'clients'        => 'fa-solid fa-address-book',
            'reports'        => 'fa-solid fa-chart-pie',
            'config'         => 'fa-solid fa-gears',
            'seguridad'      => 'fa-solid fa-shield-halved',
            'ventas'         => 'fa-solid fa-money-bill-transfer',
            'reportes'       => 'fa-solid fa-chart-pie',
            'import'         => 'fa-solid fa-file-import',
        ];

        return $icons[strtolower($entity)] ?? 'fa-solid fa-circle-dot';
    }

    /**
     * Genera el nombre del segmento/módulo para separar en el menú sin agrupar en dropdowns.
     */
    protected function generateModuleName($slug)
    {
        if ($slug === 'dashboard') return 'Tablero';

        $parts = explode('.', $slug);
        $entity = count($parts) > 1 ? $parts[count($parts) - 2] : 'General';

        $moduleMapping = [
            'usuarios'       => 'Configuración',
            'roles'          => 'Configuración',
            'permissions'    => 'Configuración',
            'products'       => 'Inventario',
            'categories'     => 'Inventario',
            'purchases'      => 'Inventario',
            'suppliers'      => 'Inventario',
            'sales'          => 'Ventas',
            'abonos'         => 'Ventas',
            'clients'        => 'Ventas',
            'reports'        => 'Análisis',
            'cash-registers' => 'Caja',
            'import'         => 'Herramientas',
        ];
        
        return $moduleMapping[strtolower($entity)] ?? 'General';
    }

    /**
     * Genera el orden sugerido.
     */
    protected function generateOrder($slug)
    {
        if ($slug === 'dashboard') return 1;
        
        $parts = explode('.', $slug);
        $entity = count($parts) > 1 ? $parts[count($parts) - 2] : 'General';

        $orderMapping = [
            'products'       => 10,
            'categories'     => 11,
            'purchases'      => 12,
            'suppliers'      => 13,
            'sales'          => 20,
            'abonos'         => 21,
            'clients'        => 22,
            'reports'        => 40,
            'cash-registers' => 30,
            'import'         => 5,
            'usuarios'       => 100,
            'roles'          => 101,
            'permissions'    => 102,
        ];

        return $orderMapping[strtolower($entity)] ?? 50;
    }
}
