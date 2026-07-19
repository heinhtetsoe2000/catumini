<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <flux:heading size="lg" class="font-bold text-2xl dark:text-ink-invert">
                {{ __('Profile') }}
            </flux:heading>

            <flux:modal.trigger name="edit-profile">
                <flux:button icon="pencil" />
            </flux:modal.trigger>
        </div>
    </x-slot>

    <div>
        <flux:card class="mx-auto m-4 w-xs sm:w-96 max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-left gap-4">
                <flux:avatar src="https://unavatar.io/x/calebporzio" />
                <div>
                    <flux:heading size="lg" class="font-bold capitalize text-2xl dark:text-ink-invert">{{ $user->name }}</flux:heading>
                    <flux:text class="text-sm text-ink-muted">{{ $user->email }}</flux:text>
                </div>
            </div>
            <flux:text class="mt-1 text-sm text-ink-muted">
                {{ __('Joined') }} {{ $user->created_at->format('M d, Y') }}
            </flux:text>
        </flux:card>

        <flux:modal name="edit-profile" class="md:w-96">
            <div class="space-y-6">
                <flux:heading size="lg">Edit Profile</flux:heading>

                <form wire:submit="save" class="space-y-4">
                    @csrf

                    <flux:input name="name" wire:model="name" placeholder="Expense name" required />
                    @error('name')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror

                    <div class="flex items-center justify-between gap-2">
                        <flux:input name="amount" type="number" wire:model="amount" placeholder="Amount (Ks)" min="0" required />
                        @error('amount')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror

                        <flux:input name="spent_on" type="date" wire:model="spent_on" required />
                        @error('spent_on')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </div>

                    <flux:textarea name="description" wire:model="description" placeholder="Description">{{ $this->description }}</flux:textarea>
                    @error('description')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror

                    <div class="flex justify-between gap-2">
                        <flux:modal.close>
                            <flux:button variant="ghost">Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button class="w-full" variant="primary" type="submit">Add Expense</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

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
