<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Middleware\EnsureCentralDomain;

// Entry point: central -> welcome, tenant -> login
Route::get('/', function () {
    if (in_array(request()->getHost(), config('tenancy.central_domains', []), true)) {
        return redirect()->route('central.login');
    }

    return redirect()->route('login');
}); // ruta de entrada

// Central Management (Owner only)
Route::prefix('central')->name('central.')->middleware(EnsureCentralDomain::class)->group(function () {
    Route::get('/login', [App\Http\Controllers\Central\Auth\CentralLoginController::class, 'create'])->name('login');
    Route::post('/login', [App\Http\Controllers\Central\Auth\CentralLoginController::class, 'store'])->name('login.submit');

    Route::post('/gate/verify', [App\Http\Controllers\Central\GateController::class, 'verifyKey'])->name('gate.verify');

    Route::middleware('auth:owner')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Central\DashboardController::class, 'index'])->name('dashboard');

        
        Route::get('/tenants/check-id', [\App\Http\Controllers\Central\TenantController::class, 'checkId'])->name('tenants.check');
        Route::post('/tenants/{tenant}/maintenance', [\App\Http\Controllers\Central\TenantController::class, 'maintenance'])->name('tenants.maintenance');
        Route::post('/tenants/{tenant}/mark-as-paid', [\App\Http\Controllers\Central\TenantController::class, 'markAsPaid'])->name('tenants.mark-as-paid');
        Route::post('/tenants/{tenant}/suspend', [\App\Http\Controllers\Central\TenantController::class, 'suspend'])->name('tenants.suspend');
        Route::resource('tenants', \App\Http\Controllers\Central\TenantController::class);

        // Global Settings
        Route::get('/settings', [\App\Http\Controllers\Central\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [\App\Http\Controllers\Central\SettingController::class, 'update'])->name('settings.update');

        // Gate Key Management
        Route::get('/gate-key', [\App\Http\Controllers\Central\GateController::class, 'editGateKey'])->name('gate_key.edit');
        Route::post('/gate-key', [\App\Http\Controllers\Central\GateController::class, 'updateGateKey'])->name('gate_key.update');

        // Mantenimiento y Comandos
        Route::get('/maintenance', [\App\Http\Controllers\Central\MaintenanceController::class, 'index'])->name('maintenance.index');
        Route::post('/maintenance/run', [\App\Http\Controllers\Central\MaintenanceController::class, 'runCommand'])->name('maintenance.run');

        // Métricas y Logs HTTP
        Route::get('/metrics', [\App\Http\Controllers\Central\MetricsController::class, 'index'])->name('metrics.index');
        Route::post('/metrics/clear', [\App\Http\Controllers\Central\MetricsController::class, 'clearLogs'])->name('metrics.clear');

        // Payment Notifications
        Route::get('/payment-notifications', [\App\Http\Controllers\Central\PaymentNotificationController::class, 'index'])->name('payment-notifications.index');
        Route::get('/payment-notifications/{notification}', [\App\Http\Controllers\Central\PaymentNotificationController::class, 'show'])->name('payment-notifications.show');
        Route::get('/payment-notifications/{notification}/download', [\App\Http\Controllers\Central\PaymentNotificationController::class, 'download'])->name('payment-notifications.download');
        Route::post('/payment-notifications/{notification}/review', [\App\Http\Controllers\Central\PaymentNotificationController::class, 'markAsReviewed'])->name('payment-notifications.review');
        Route::delete('/payment-notifications/{notification}', [\App\Http\Controllers\Central\PaymentNotificationController::class, 'destroy'])->name('payment-notifications.destroy');
    });
    
    Route::post('/logout', [App\Http\Controllers\Central\Auth\CentralLoginController::class, 'destroy'])->name('logout');
});

// Perfil y Seguridad (Universal)
Route::middleware([EnsureCentralDomain::class, 'auth:owner'])->group(function () {
    Route::put('/central/password-update', [\App\Http\Controllers\Profile\PasswordController::class, 'update'])->name('password.update.ajax');
});

// Redirect /login to /central/login ONLY on central domains
// This prevents conflicting with tenant /login routes
foreach (config('tenancy.central_domains', []) as $domain) {
    Route::domain($domain)->match(['get', 'post'], '/login', function () {
        if (request()->isMethod('post')) {
            return redirect()->route('central.login.submit')->withInput();
        }
        return redirect()->route('central.login');
    });
}


