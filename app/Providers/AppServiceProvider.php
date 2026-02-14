<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Usuarios\Usuario;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route; // Añadido
use App\Models\CentralUser; // Añadido

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

        Gate::before(function (Usuario $user, string $ability) {
            return $user->hasPermission($ability) ?: null;
        });

        // Vinculación explícita del modelo para CentralUser
        Route::model('user', CentralUser::class);
    }
}
