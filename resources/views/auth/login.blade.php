<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <flux:input
            id="email"
            type="email"
            name="email"
            label="Email"
            value="{{ old('email') }}"
            required
            autofocus
            autocomplete="username"
        />

        <flux:input
            id="password"
            type="password"
            name="password"
            label="Password"
            required
            autocomplete="current-password"
        />

        <label class="flex items-center gap-2 text-sm text-ink-muted dark:text-ink-soft">
            <input id="remember_me" type="checkbox" name="remember" class="rounded border-ink/20 text-accent">
            {{ __('Remember me') }}
        </label>

        <div class="flex items-center justify-end gap-3">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-ink-muted underline hover:text-ink dark:text-ink-soft dark:hover:text-ink-invert">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <flux:button type="submit" variant="primary">
                {{ __('Log in') }}
            </flux:button>
        </div>
    </form>
</x-guest-layout>
