<section>
    <header>
        <flux:heading size="lg">{{ __('Update Password') }}</flux:heading>
        <flux:subheading class="mt-1">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </flux:subheading>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <flux:input
                id="update_password_current_password"
                name="current_password"
                type="password"
                label="{{ __('Current Password') }}"
                autocomplete="current-password"
            />
            <flux:error name="current_password" bag="updatePassword" />
        </div>

        <div>
            <flux:input
                id="update_password_password"
                name="password"
                type="password"
                label="{{ __('New Password') }}"
                autocomplete="new-password"
            />
            <flux:error name="password" bag="updatePassword" />
        </div>

        <div>
            <flux:input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                label="{{ __('Confirm Password') }}"
                autocomplete="new-password"
            />
            <flux:error name="password_confirmation" bag="updatePassword" />
        </div>

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>

            @if (session('status') === 'password-updated')
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
