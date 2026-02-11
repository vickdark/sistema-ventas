<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'central_domain' => \App\Http\Middleware\EnsureCentralDomain::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'permissions/sync',
            'central/tenants/*/maintenance' // Also useful to exclude standard maintenance toggles if triggered via API
        ]);
        $middleware->redirectGuestsTo(function ($request) {
            if (in_array($request->getHost(), config('tenancy.central_domains', []))) {
                return route('central.login');
            }
            return route('login');
        });

        // Prevenir caché del botón atrás
        $middleware->append(\App\Http\Middleware\PreventBackHistory::class);
        $middleware->append(\App\Http\Middleware\LogRequests::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
