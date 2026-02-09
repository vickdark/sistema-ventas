# Guía de Implementación de Multitenencia en Laravel 12 (Estrategia de Una DB por Inquilino)

Este documento detalla el proceso completo y definitivo para configurar la multitenencia en Laravel 12 utilizando el paquete `stancl/tenancy` v3.

## 1. Prerrequisitos y Entorno
*   **Laravel 12+**
*   **MySQL** (Servidor activo)
*   **Dominios locales:** Configuración de dominios wildcard (ej. `*.laravel-multitenancy.test`) en tu servidor local (Herd/Valet) y en el archivo `hosts`.

## 2. Instalación del Paquete
Ejecuta el comando para instalar la dependencia:
```bash
composer require stancl/tenancy
```
Luego, inicializa la configuración:
```bash
php artisan tenancy:install
```
Esto creará archivos clave: `config/tenancy.php`, `app/Providers/TenancyServiceProvider.php`, y el directorio `database/migrations/tenant`.

## 3. Configuración de Base de Datos (Conexiones)
En Laravel 12, es vital definir las conexiones `central` y `tenant` en `config/database.php`.

### Configuración en `.env`
```dotenv
DB_CONNECTION=central
DB_DATABASE=usuariosmultitenancy # Base de datos de control
```

### Configuración en `config/database.php`
```php
'default' => env('DB_CONNECTION', 'central'),

'connections' => [
    'central' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE'),
        'username' => env('DB_USERNAME'),
        'password' => env('DB_PASSWORD'),
        // ... otros parámetros estándar
    ],

    'tenant' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => null, // Dinámico
        'username' => env('DB_USERNAME'),
        'password' => env('DB_PASSWORD'),
        // ... otros parámetros estándar
    ],
],
```

## 4. El Modelo Tenant (PASO CRÍTICO)
Para evitar errores de "métodos no definidos" como `run()` o `database()`, el modelo debe implementar la interfaz correcta y usar los traits necesarios.

**Archivo:** `app/Models/Tenant.php`
```php
namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDomains, HasDatabase;
}
```

## 5. Registro de Proveedores (Laravel 12)
Al igual que en la versión anterior, debes registrar manualmente el proveedor de servicios de multitenencia.

**Archivo:** `bootstrap/providers.php`
```php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\TenancyServiceProvider::class, // <-- Obligatorio
];
```

## 6. Configuración de Tenancy
Asegúrate de que el paquete use tu modelo personalizado y reconozca tus dominios.

**Archivo:** `config/tenancy.php`
```php
'tenant_model' => \App\Models\Tenant::class,

'central_domains' => [
    '127.0.0.1',
    'localhost',
    'laravel-multitenancy.test',
],

'database' => [
    'central_connection' => 'central',
    'template_tenant_connection' => 'tenant',
],
```

## 7. Gestión de Migraciones
Separa tus tablas de lógica de negocio de las tablas de control central.

*   **Migraciones Centrales:** Van en `database/migrations`. Aquí deben estar `tenants` y `domains`.
*   **Migraciones de Inquilinos:** Van en `database/migrations/tenant`. Aquí van `users`, `roles`, `permissions`, etc.

Ejecutar migraciones centrales:
```bash
php artisan migrate
```

## 8. Scripts de Gestión de Inquilinos
Utiliza estos scripts para automatizar la creación y actualización de empresas.

### Registro (`register_tenant.php`)
Usa `setInternal('db_name', ...)` para definir nombres de DB personalizados.
```php
$tenant = Tenant::firstOrCreate(['id' => 'empresa_a']);
$tenant->setInternal('db_name', 'usuariosmultitenancy_empresa_a');
$tenant->save();

$tenant->domains()->create(['domain' => 'empresa_a.laravel-multitenancy.test']);
```

### Migración y Seeders (`migrate_tenant.php`)
```php
$tenant->run(function () {
    // Migraciones
    Artisan::call('migrate', ['--path' => 'database/migrations/tenant', '--force' => true]);
    // Seeders
    Artisan::call('db:seed', ['--force' => true]);
});
```

## 9. Herramientas de Diagnóstico
Para verificar que el sistema está funcionando y la conexión cambia correctamente:

*   **`tools/inspect_tenant.php`**: Muestra los detalles internos del objeto Tenant y si implementa las interfaces correctas.
*   **`tools/check_tenant_db.php`**: Verifica la conexión real a la base de datos del inquilino y lista las tablas existentes.

## 10. Seguridad y Aislamiento (Dominios vs Subdominios)
Para evitar que un usuario de un inquilino acceda al panel de administración central, se implementó un middleware de protección.

### Crear Middleware `EnsureCentralDomain`
```php
public function handle(Request $request, Closure $next) {
    $centralDomains = config('tenancy.central_domains', []);
    if (!in_array($request->getHost(), $centralDomains)) {
        abort(404);
    }
    return $next($request);
}
```
### Aplicar a las rutas centrales (`web.php`)
```php
Route::middleware('central_domain')->group(function () {
    // Rutas de administración central
    require __DIR__.'/auth.php';
});
```

## 11. Infraestructura Base (Base de Datos Central)
Si usas `SESSION_DRIVER=database` o `CACHE_STORE=database`, la base de datos central **DEBE** tener las tablas de sistema.
1. `php artisan make:cache-table`
2. `php artisan make:session-table`
3. `php artisan make:queue-table`
4. `php artisan migrate`

## 12. Configuración de Subdominios (LOCAL vs PRODUCCIÓN)

> [!IMPORTANT]
> Los navegadores y sistemas operativos manejan la resolución de nombres de forma distinta según el entorno.

### En Local (Windows/Mac/Linux)
Los subdominios **no funcionan automáticamente** en local porque el archivo `hosts` no acepta comodines (`*`).

1.  **Opción Manual (Archivo Hosts):**
    Añade cada subdominio manualmente en `C:\Windows\System32\drivers\etc\hosts`:
    ```text
    127.0.0.1 laravel-multitenancy.test
    127.0.0.1 victorcardoza.laravel-multitenancy.test
    ```
2.  **Opción Automática (.localhost):**
    Usa el TLD `.localhost` (ej: `empresa.laravel-multitenancy.localhost`). Todos los navegadores modernos redirigen cualquier subdominio de `.localhost` a `127.0.0.1` de forma automática sin configurar nada.

### En Producción (Servidor Real)
En un servidor real, **SÍ** se usan comodines para que no tengas que configurar nada cada vez que creas una empresa.

1.  **DNS (Cloudflare/GoDaddy/etc.):**
    Crea un registro tipo **A** con el nombre `*` apuntando a la IP de tu servidor.
    *   Ejemplo: `*  A  1.2.3.4`
2.  **Web Server (Nginx/Apache):**
    Configura el bloque `server_name` para que acepte el comodín.
    *   Nginx: `server_name .tudominio.com;` (el punto inicial actúa como comodín).

## 13. Panel de Mantenimiento Avanzado
Se implementó una consola de terminal en tiempo real para ejecutar `migrate` y `db:seed` de forma individual por inquilino desde la interfaz de edición. El sistema analiza la salida de Artisan y muestra un resumen de cambios (Migraciones / Seeders) ejecutados.

## 14. UI Dinámica
El sidebar (`aside.blade.php`) detecta automáticamente el contexto:
*   Si es inquilino: Muestra el ID de la empresa y oculta los menús técnicos (`Tenancy`, `Central`).
*   Si es central: Muestra la marca global y las herramientas de gestión de inquilinos.

## 15. Arquitectura de Rutas Central vs Inquilino (IMPORTANTE)
Para evitar conflictos de autenticación y errores 419 (CSRF), es crucial separar estrictamente las rutas.

### Archivo `routes/web.php` (Solo Central)
Debe contener ÚNICAMENTE las rutas del dominio central (login del dueño, dashboard central).
```php
// Ejemplo correcto
Route::prefix('central')->group(function() {
    Route::get('/login', ...)->name('central.login');
    Route::post('/logout', ...)->name('logout'); // Nota: el nombre final es central.logout
});
```

### Archivo `routes/tenant.php` (Solo Inquilinos)
Debe contener TODAS las rutas funcionales de la aplicación (usuarios, roles, permisos, dashboard del cliente).
```php
Route::middleware(['web', InitializeTenancyByDomain::class])->group(function() {
    Route::middleware('auth')->group(function() {
        Route::post('permissions/sync', ...)->name('permissions.sync');
        Route::resource('users', ...);
    });
});
```
> **Nota:** Nunca mezcles rutas de inquilino en `web.php` porque el middleware `central_domain` bloqueará el acceso a los subdominios.

### Excepciones CSRF (`bootstrap/app.php`)
Para rutas críticas de sistema o API interna (como `permissions/sync`), puede ser necesario excluir la verificación CSRF si se presentan problemas de sesión (error 419).
```php
$middleware->validateCsrfTokens(except: [
    'permissions/sync',
    'central/tenants/*/maintenance'
]);
```
