<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            if (
                in_array($request->getHost(), config('tenancy.central_domains', []), true)
                || $request->routeIs('central.*')
            ) {
                return route('central.login');
            }
            return route('login');
        }

        return null;
    }
}
