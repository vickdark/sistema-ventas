<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Models\CentralSetting;

class GateController extends Controller
{
    /**
     * Verify the gate key submitted by the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyKey(Request $request)
    {
        $this->ensureIsNotRateLimited($request);

        $request->validate([
            'gate_key' => 'required|string',
            'g-recaptcha-response' => 'required|captcha',
        ]);

        $correctKey = CentralSetting::where('key', 'central_login_gate_key')->value('value');

        if ($request->input('gate_key') === $correctKey) {
            RateLimiter::clear($this->throttleKey($request));
            Session::put('central_gate_passed', true);
            return redirect()->route('central.login')->with('status', '¡Clave correcta! Acceso concedido.');
        }

        RateLimiter::hit($this->throttleKey($request));

        throw ValidationException::withMessages([
            'gate_key' => 'Clave de acceso incorrecta. ' . $this->getRemainingAttemptsMessage($request),
        ])->redirectTo(route('central.login'));
    }

    /**
     * Ensure the gate key request is not rate limited.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'gate_key' => "Demasiados intentos de clave de acceso. Por favor, inténtalo de nuevo en {$seconds} segundos.",
        ])->redirectTo(route('central.login'));
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function throttleKey(Request $request): string
    {
        return 'gate-key-attempt|' . $request->ip();
    }

    /**
     * Get the message for remaining attempts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function getRemainingAttemptsMessage(Request $request): string
    {
        $remaining = RateLimiter::retriesLeft($this->throttleKey($request), 5);
        if ($remaining > 0) {
            return "Te quedan {$remaining} intentos.";
        }
        $seconds = RateLimiter::availableIn($this->throttleKey($request));
        return "Has excedido el número máximo de intentos. Por favor, inténtalo de nuevo en " . ceil($seconds / 60) . " minutos.";
    }

    /**
     * Show the form for editing the central login gate key.
     *
     * @return \Illuminate\View\View
     */
    public function editGateKey()
    {
        $gateKeySetting = CentralSetting::where('key', 'central_login_gate_key')->first();
        $currentKey = $gateKeySetting ? $gateKeySetting->value : '';

        // Obtener también los datos para la vista de settings general
        $adminEmails = CentralSetting::where('key', 'admin_payment_emails')->value('value');

        return view('central.settings.index', compact('currentKey', 'adminEmails'));
    }

    /**
     * Update the central login gate key.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateGateKey(Request $request)
    {
        $request->validate([
            'gate_key' => 'required|string|min:4',
        ]);

        CentralSetting::updateOrCreate(
            ['key' => 'central_login_gate_key'],
            ['value' => $request->input('gate_key'), 'description' => 'Clave de acceso para el login central']
        );

        Session::flash('status', 'La clave de acceso ha sido actualizada correctamente.');

        return redirect()->route('central.settings.index');
    }
}
