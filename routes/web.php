<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Middleware\EnsureCentralDomain;

Route::middleware(EnsureCentralDomain::class)->group(function () {
    Route::get('/', WelcomeController::class);
});

// Central Management (Owner only)
Route::prefix('central')->name('central.')->middleware(EnsureCentralDomain::class)->group(function () {
    Route::get('/login', [App\Http\Controllers\Central\Auth\CentralLoginController::class, 'create'])->name('login');
    Route::post('/login', [App\Http\Controllers\Central\Auth\CentralLoginController::class, 'store'])->name('login.submit');

    Route::middleware('auth:owner')->group(function () {
        Route::get('/dashboard', function() {
            return redirect()->route('central.tenants.index');
        })->name('dashboard');
        
        Route::get('/tenants/check-id', [\App\Http\Controllers\Central\TenantController::class, 'checkId'])->name('tenants.check');
        Route::post('/tenants/{tenant}/maintenance', [\App\Http\Controllers\Central\TenantController::class, 'maintenance'])->name('tenants.maintenance');
        Route::resource('tenants', \App\Http\Controllers\Central\TenantController::class);
    });
    
    Route::post('/logout', [App\Http\Controllers\Central\Auth\CentralLoginController::class, 'destroy'])->name('logout');
});

// Perfil y Seguridad (Universal)
Route::middleware([EnsureCentralDomain::class, 'auth:owner'])->group(function () {
    Route::put('/central/password-update', [\App\Http\Controllers\Profile\PasswordController::class, 'update'])->name('password.update.ajax');
});

Route::match(['get', 'post'], '/login', function () {
    if (in_array(request()->getHost(), config('tenancy.central_domains'))) {
        if (request()->isMethod('post')) {
            // Note: If you reach here, it means you submitted to /login instead of /central/login
            return redirect()->route('central.login.submit')->withInput();
        }
        return redirect()->route('central.login');
    }
    abort(404);
});

Route::get('/welcome', WelcomeController::class)->name('welcome');


