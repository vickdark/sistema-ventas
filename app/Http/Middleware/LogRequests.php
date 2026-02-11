<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\HttpLog;

class LogRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        $duration = microtime(true) - $startTime;

        // Log everything except common assets and debugger routes if any
        if (!$request->is('livewire/*') && !$request->is('sanctum/*') && !$request->is('img/*')) {
            try {
                HttpLog::create([
                    'tenant_id' => function_exists('tenant') && tenant() ? tenant('id') : null,
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                    'status' => $response->getStatusCode(),
                    'duration' => $duration,
                    'ip' => $request->ip(),
                ]);
            } catch (\Exception $e) {
                // Fail silently to not break the app
            }
        }

        return $response;
    }
}
