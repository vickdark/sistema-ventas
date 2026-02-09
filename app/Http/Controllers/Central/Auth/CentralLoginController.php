<?php

namespace App\Http\Controllers\Central\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
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
        if (! Auth::guard('owner')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route('central.tenants.index');
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
