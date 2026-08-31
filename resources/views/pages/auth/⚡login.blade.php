<?php

use Flux\Flux;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::guest')] class extends Component
{
    public string $email;

    public string $password;

    public bool $rememberMe = false;

    public function login()
    {
        $validated = $this->validate([
            'email' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'rememberMe' => 'required|boolean'
        ]);

        $validated['email'] = trim($validated['email']);
        $validated['password'] = trim($validated['password']);

        $this->authenticate();

        session()->regenerate();

        Flux::toast(variant: 'success', text: __('Logged in successfully'));

        return $this->redirect('/home', navigate: true);
    }

        /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->rememberMe)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
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

        event(new Lockout(request()));

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
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
};
?>

<div>
    <form wire:submit="login" class="space-y-6">
        @csrf

        <flux:input
            id="email"
            type="email"
            name="email"
            :label="__('Email')"
            wire:model="email"
            required
            autofocus
            autocomplete="username"
        />

        <flux:input
            id="password"
            type="password"
            name="password"
            :label="__('Password')"
            wire:model="password"
            required
            autocomplete="current-password"
        />

        <label class="flex items-center gap-2 text-sm dark:text-ink-soft">
            <input id="remember_me" type="checkbox" name="remember" wire:model="rememberMe" class="rounded border-ink/20 text-accent">
            {{ __('Remember me') }}
        </label>

        <div class="flex items-center justify-end gap-3">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm underline hover:text-ink dark:text-ink-soft dark:hover:text-ink-invert">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <flux:button type="submit" variant="primary" color="zinc">
                {{ __('Log in') }}
            </flux:button>
        </div>
    </form>
</div>