<x-app-layout>
    <x-slot name="header">
        <flux:heading size="lg" class="!font-serif text-ink dark:text-ink-invert">
            {{ __('Profile') }}
        </flux:heading>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="border border-ink/10 bg-paper-elevated p-4 sm:rounded-lg sm:p-8 dark:border-ink-invert/10 dark:bg-paper-dark-elevated">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="border border-ink/10 bg-paper-elevated p-4 sm:rounded-lg sm:p-8 dark:border-ink-invert/10 dark:bg-paper-dark-elevated">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="border border-ink/10 bg-paper-elevated p-4 sm:rounded-lg sm:p-8 dark:border-ink-invert/10 dark:bg-paper-dark-elevated">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
