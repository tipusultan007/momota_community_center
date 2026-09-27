<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
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
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $login = $this->input('login') ?? $this->input('phone') ?? $this->input('email');
        if ($login) {
            $this->merge([
                'login' => trim($login),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login = trim($this->input('login') ?? $this->input('phone') ?? $this->input('email') ?? '');
        $password = $this->input('password');
        $remember = $this->boolean('remember');

        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);
        $authenticated = false;

        if ($isEmail) {
            $authenticated = Auth::attempt(['email' => $login, 'password' => $password], $remember);
        } else {
            // Attempt 1: Direct match by phone
            $authenticated = Auth::attempt(['phone' => $login, 'password' => $password], $remember);

            // Attempt 2: Match phone variations (+880..., 880..., 01...)
            if (! $authenticated) {
                $variants = [];
                if (str_starts_with($login, '+880')) {
                    $variants[] = '0' . substr($login, 4);
                    $variants[] = substr($login, 3);
                } elseif (str_starts_with($login, '880')) {
                    $variants[] = '0' . substr($login, 3);
                    $variants[] = '+' . $login;
                } elseif (str_starts_with($login, '01')) {
                    $variants[] = '+88' . $login;
                    $variants[] = '88' . $login;
                }

                foreach ($variants as $variant) {
                    if (Auth::attempt(['phone' => $variant, 'password' => $password], $remember)) {
                        $authenticated = true;
                        break;
                    }
                }
            }

            // Fallback attempt: by email in case of unconventional email address
            if (! $authenticated) {
                $authenticated = Auth::attempt(['email' => $login, 'password' => $password], $remember);
            }
        }

        if (! $authenticated) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
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
        $login = $this->input('login') ?? $this->input('phone') ?? $this->input('email') ?? '';
        return Str::transliterate(Str::lower($login).'|'.$this->ip());
    }
}
