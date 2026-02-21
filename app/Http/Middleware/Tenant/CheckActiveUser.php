<?php

namespace App\Http\Middleware\Tenant;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckActiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !Auth::user()->is_active) {
            Auth::logout();
            
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Su cuenta ha sido desactivada.'], 403);
            }
            
            return redirect()->route('login')->withErrors(['email' => 'Su cuenta ha sido desactivada.']);
        }

        return $next($request);
    }
}
