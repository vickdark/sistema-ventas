<?php

namespace App\Http\Controllers\Central\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerifyEmailController extends Controller
{
    /**
     * Show the email verification notice.
     */
    public function notice(): View
    {
        return view('central.auth.verify-email');
    }

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verify(Request $request): RedirectResponse
    {
        $user = \App\Models\CentralUser::find($request->route('id'));

        if (!$user) {
            abort(404); // O redirigir a una página de error
        }

        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            throw new \Illuminate\Auth\Access\AuthorizationException();
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('central.dashboard', absolute: false))->with('status', 'Tu correo electrónico ya ha sido verificado.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->route('central.login')->with('status', '¡Gracias por verificar tu correo electrónico! Ahora puedes iniciar sesión.');
    }

    /**
     * Resend the email verification notification.
     */
    public function send(Request $request): RedirectResponse
    {
        if ($request->user('owner')->hasVerifiedEmail()) {
            return redirect()->intended(route('central.dashboard', absolute: false))->with('status', 'Tu correo electrónico ya ha sido verificado.');
        }

        $request->user('owner')->sendEmailVerificationNotification();

        return redirect()->route('central.login')->with('status', '¡Se ha enviado un nuevo enlace de verificación a tu dirección de correo electrónico!');
    }
}
