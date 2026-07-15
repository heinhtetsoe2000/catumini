<section>
    <header>
        <flux:heading size="lg">{{ __('Profile Information') }}</flux:heading>
        <flux:subheading class="mt-1">
            {{ __("Update your account's profile information and email address.") }}
        </flux:subheading>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <flux:input
                id="name"
                name="name"
                type="text"
                label="{{ __('Name') }}"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
                autocomplete="name"
            />
            <flux:error name="name" />
        </div>

        <div>
            <flux:input
                id="email"
                name="email"
                type="email"
                label="{{ __('Email') }}"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
            />
            <flux:error name="email" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <flux:text class="mt-2">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="text-sm text-ink-muted underline hover:text-ink dark:text-ink-soft dark:hover:text-ink-invert">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </flux:text>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-ink-muted"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
