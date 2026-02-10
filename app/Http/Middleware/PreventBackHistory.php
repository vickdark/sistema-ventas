<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventBackHistory
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Detectar si es una petición AJAX/JSON
        $isAjax = $request->ajax() || 
                  $request->wantsJson() || 
                  $request->expectsJson() ||
                  $request->header('X-Requested-With') === 'XMLHttpRequest';

        $contentType = $response->headers->get('Content-Type', '');
        $isJsonResponse = str_contains($contentType, 'application/json');

        // Para respuestas AJAX/JSON: headers MUY agresivos
        if ($isAjax || $isJsonResponse) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            $response->headers->set('Vary', 'X-Requested-With, Accept');
            
            // Header adicional para evitar que el navegador use esta respuesta en el historial
            $response->headers->set('X-Content-Type-Options', 'nosniff');
        } else {
            // Para HTML normal: headers moderados
            $response->headers->set('Cache-Control', 'no-cache, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
        }

        return $response;
    }
}
