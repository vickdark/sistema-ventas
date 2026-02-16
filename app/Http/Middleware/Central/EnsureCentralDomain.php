<?php

namespace App\Http\Middleware\Central;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCentralDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $centralDomains = config('tenancy.central_domains', []);
        
        if (!in_array($request->getHost(), $centralDomains)) {
            abort(404);
        }

        return $next($request);
    }
}
