<?php

namespace App\Http\Controllers\Central\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Auth\LoginRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class CentralLoginController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('central.auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->ensureIsNotRateLimited();

        if (! Auth::guard('owner')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::hit($request->throttleKey());

            $remaining = RateLimiter::retriesLeft($request->throttleKey(), 5);
            $message = trans('auth.failed');
            if ($remaining > 0) {
                $message .= " Te quedan {$remaining} intentos.";
            }

            throw ValidationException::withMessages(['email' => $message]);
        }

        RateLimiter::clear($request->throttleKey());

        $request->session()->regenerate();

        return redirect()->route('central.dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('owner')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('central.login');
    }
}
