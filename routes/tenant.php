<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    // Only register these routes if we are NOT on a central domain.
    // This prevents these routes from hijacking the central domain routes.
    if (!in_array(request()->getHost(), config('tenancy.central_domains', []))) {
        Route::get('/', function () {
            return redirect()->route('login');
        });

        // Tenant Login/Auth
        require __DIR__.'/auth.php';

        Route::middleware('auth')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
            Route::get('/dashboard/admin', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.admin');
            
            Route::resources([
                'usuarios' => \App\Http\Controllers\Usuarios\UsuarioController::class,
                'roles' => \App\Http\Controllers\Roles\RoleController::class,
                'clients' => \App\Http\Controllers\Tenant\ClientController::class,
                'products' => \App\Http\Controllers\Tenant\ProductController::class,
                'categories' => \App\Http\Controllers\Tenant\CategoryController::class,
                'purchases' => \App\Http\Controllers\Tenant\PurchaseController::class,
                'suppliers' => \App\Http\Controllers\Tenant\SupplierController::class,
            ]);

            // Gestión de Roles y Seguridad (Rutas adicionales)
            Route::get('roles/{role}/permisos', [\App\Http\Controllers\Roles\RoleController::class, 'permissions'])->name('roles.edit_permissions');
            Route::put('roles/{role}/permisos', [\App\Http\Controllers\Roles\RoleController::class, 'updateRolePermissions'])->name('roles.update_permissions');
            
            // Gestión de Permisos (Sincronización)
            Route::post('permissions/sync', [\App\Http\Controllers\Roles\PermissionController::class, 'sync'])->name('permissions.sync');
            // Perfil y Seguridad
            Route::put('/password', [\App\Http\Controllers\Profile\PasswordController::class, 'update'])->name('password.update.ajax');
        });
    }
});
