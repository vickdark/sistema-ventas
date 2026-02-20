<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Tenant\Usuario;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route; // Añadido
use App\Models\Central\CentralUser; // Añadido

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Gate::before(function ($user, string $ability) {
            return $user->hasPermission($ability) ?: null;
        });

        // Vinculación explícita del modelo para CentralUser
        Route::model('user', CentralUser::class);
        
        // Vinculación explícita para Tenant
        Route::model('tenant', \App\Models\Central\Tenant::class);

        // Observers
        \App\Models\Tenant\Sale::observe(\App\Observers\Tenant\SaleObserver::class);
        \App\Models\Tenant\Purchase::observe(\App\Observers\Tenant\PurchaseObserver::class);
        \App\Models\Tenant\Expense::observe(\App\Observers\Tenant\ExpenseObserver::class);
    }
}
