<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        // ✅ L'utilisateur est maintenant connecté
        $user = Auth::user();

        // Vérification du rôle
        if ($user->hasRole('Admin') or $user->hasRole('Super Gestionnaire') or $user->hasRole('Gestionnaire Multi-Sites')) {
            // Pour un admin, tu peux stocker un flag ou rediriger vers une page pour choisir la station
            session(['needs_station_selection' => true]);

            // Optionnel : tu peux aussi stocker un message ou une variable temporaire ici
            return;
        }

        // Pour les non-admins, on vérifie qu'ils sont associés à au moins une station
        if (! $user->stations()->exists()) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Aucune station n\'est associée à votre compte.',
            ]);
        }

        // Si oui, on peut stocker la première station associée dans la session
        $firstStationId = $user->stations()->first()->id ?? null;
        session(['selected_station_id' => $firstStationId]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
