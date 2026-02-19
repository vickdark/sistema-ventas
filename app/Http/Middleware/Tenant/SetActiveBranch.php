<?php

namespace App\Http\Middleware\Tenant;

use App\Models\Tenant\Branch;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetActiveBranch
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si la sucursal en sesión es válida
        if (Session::has('active_branch_id')) {
            $exists = Branch::where('id', Session::get('active_branch_id'))->exists();
            if (!$exists) {
                Session::forget('active_branch_id');
            }
        }

        // Si no hay sucursal activa, buscar una
        if (auth()->check() && !Session::has('active_branch_id')) {
            $user = auth()->user();
            
            // 1. Usar la sucursal asignada al usuario si es válida
            if ($user->branch_id && Branch::where('id', $user->branch_id)->exists()) {
                Session::put('active_branch_id', $user->branch_id);
            } else {
                // 2. Usar la sucursal principal
                $mainBranch = Branch::where('is_main', true)->first();
                
                if ($mainBranch) {
                    Session::put('active_branch_id', $mainBranch->id);
                } else {
                    // 3. Fallback a la primera sucursal disponible
                    $firstBranch = Branch::first();
                    if ($firstBranch) {
                        Session::put('active_branch_id', $firstBranch->id);
                    }
                }
            }
        }

        return $next($request);
    }
}
