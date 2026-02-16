<?php

namespace App\Http\Requests\Central\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'g-recaptcha-response' => ['required', 'captcha'],
        ];
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }

    /**
     * Ensure the login request is not rate limited.
     * Note: This logic is partially duplicated in the controller or should be used there.
     * Keeping helper methods for RateLimiting if the controller wants to use them.
     */
     // El controlador central implementa su propia lógica de rate limiting usando Auth::guard('owner')
     // pero llama a $request->ensureIsNotRateLimited(). Así que debo mantener este método.
    public function ensureIsNotRateLimited(): void
    {
        if (! \Illuminate\Support\Facades\RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new \Illuminate\Auth\Events\Lockout($this));

        $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($this->throttleKey());

        throw \Illuminate\Validation\ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }
}
