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
                'sales' => \App\Http\Controllers\Tenant\SaleController::class,
                'abonos' => \App\Http\Controllers\Tenant\AbonoController::class,
                'cash-registers' => \App\Http\Controllers\Tenant\CashRegisterController::class,
                'reports' => \App\Http\Controllers\Tenant\ReportController::class,
            ]);
            
            // Purchase Voucher
            Route::get('purchases/{purchase}/voucher', [\App\Http\Controllers\Tenant\PurchaseController::class, 'voucher'])->name('purchases.voucher');

            // Sale Ticket
            Route::get('sales/{sale}/ticket', [\App\Http\Controllers\Tenant\SaleController::class, 'ticket'])->name('sales.ticket');

            Route::get('abonos/pending-sales/{client}', [\App\Http\Controllers\Tenant\AbonoController::class, 'getPendingSales'])->name('abonos.pending-sales');
            Route::get('abonos/debt-summary/{client}', [\App\Http\Controllers\Tenant\AbonoController::class, 'getDebtSummary'])->name('abonos.debt-summary');
            Route::get('abonos/client-history/{client}', [\App\Http\Controllers\Tenant\AbonoController::class, 'getClientAbonoHistory'])->name('abonos.client-history');

            Route::get('cash-registers/{cash_register}/close', [\App\Http\Controllers\Tenant\CashRegisterController::class, 'closeForm'])->name('cash-registers.close-form');
            Route::post('cash-registers/{cash_register}/close', [\App\Http\Controllers\Tenant\CashRegisterController::class, 'close'])->name('cash-registers.close');

            // ETL Import Module
            Route::get('import', [\App\Http\Controllers\Tenant\ImportController::class, 'index'])->name('import.index');
            Route::get('import/template/{module}', [\App\Http\Controllers\Tenant\ImportController::class, 'template'])->name('import.template');
            Route::post('import/categories', [\App\Http\Controllers\Tenant\ImportController::class, 'importCategories'])->name('import.categories');
            Route::post('import/clients', [\App\Http\Controllers\Tenant\ImportController::class, 'importClients'])->name('import.clients');
            Route::post('import/suppliers', [\App\Http\Controllers\Tenant\ImportController::class, 'importSuppliers'])->name('import.suppliers');
            Route::post('import/products', [\App\Http\Controllers\Tenant\ImportController::class, 'importProducts'])->name('import.products');
            Route::post('import/purchases', [\App\Http\Controllers\Tenant\ImportController::class, 'importPurchases'])->name('import.purchases');

            Route::post('configurations', [\App\Http\Controllers\Tenant\ConfigurationController::class, 'update'])->name('configurations.update');

            // Gestión de Roles y Seguridad (Rutas adicionales)
            Route::get('roles/{role}/permisos', [\App\Http\Controllers\Roles\RoleController::class, 'permissions'])->name('roles.edit_permissions');
            Route::put('roles/{role}/permisos', [\App\Http\Controllers\Roles\RoleController::class, 'updateRolePermissions'])->name('roles.update_permissions');
            
            // Gestión de Permisos (Sincronización)
            Route::post('permissions/sync', [\App\Http\Controllers\Roles\PermissionController::class, 'sync'])->name('permissions.sync');
            
            // Notificaciones
            Route::get('notifications/low-stock', [\App\Http\Controllers\Tenant\NotificationController::class, 'getLowStockProducts'])->name('notifications.low-stock');

            // Perfil y Seguridad
            Route::put('/password', [\App\Http\Controllers\Profile\PasswordController::class, 'update'])->name('password.update.ajax');
        });
    }
});
