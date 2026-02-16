<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('tenant.auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Verificar si el usuario existe y si no es administrador
        $user = Usuario::where('email', $request->email)->first();

        if ($user) {
            // Asumiendo que el slug del rol de administrador es 'admin'
            if (!$user->role || $user->role->slug !== 'admin') {
                return back()->with('restricted_role', 'No tienes permisos para restablecer tu contraseña de forma autónoma. Por favor, contacta con el administrador del sistema o con gerencia para solucionar problemas con tu acceso.');
            }
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
