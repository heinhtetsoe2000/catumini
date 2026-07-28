<?php

use Livewire\Component;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Support\ExpenseDayLabel;

new class extends Component
{
    public User $user;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $new_password = '';

    public string $new_password_confirmation = '';

    public function mount()
    {
        $this->user = auth()->user();
        $this->name = $this->user->name;
        $this->email = $this->user->email;
    }

    public function save()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$this->user->id,
        ]);

        $this->user->update($validated);

        $this->modal('edit-profile')->close();
        Flux::toast(variant: 'success', text: __('Profile updated successfully'));
    }

    public function updatePassword()
    {
        $validated = $this->validate([
            'password' => 'required|string|max:255',
            'new_password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised(3)],
        ]);

        if (! Hash::check($validated['password'], $this->user->password)) {
            return $this->addError('password', __('The provided password is incorrect.'));
        }

        $this->user->password = $validated['new_password'];
        $this->user->save();

        $this->modal('update-password')->close();
        Flux::toast(variant: 'success', text: __('Password updated successfully'));
    }

    public function deleteAccount()
    {
        $validated = $this->validate([
            'password' => 'required|string|max:255',
        ]);

        if (! Hash::check($validated['password'], $this->user->password)) {
            return $this->addError('password', __('The provided password is incorrect.'));
        }

        Auth::logout();

        $this->user->delete();

        $this->modal('delete-account')->close();
        Flux::toast(variant: 'success', text: __('Account deleted successfully'));

        return redirect()->route('login');
    }

    public function logout()
    {
        Auth::guard('web')->logout();

        session()->invalidate();
        session()->regenerateToken();

        return redirect('/');
    }
};
?>

<div>
    <flux:card class="mx-auto m-4 w-90 md:w-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-left gap-4">
            <flux:avatar name="{{ $user->name }}" size="xl" color="auto" />
            <div>
                <flux:heading size="lg" class="font-bold capitalize text-2xl dark:text-ink-invert">{{ $user->name }}</flux:heading>
                <flux:text class="text-sm">{{ $user->email }}</flux:text>
            </div>
        </div>
        <flux:text class="mt-2 mb-4 text-sm">
            {{ __('Joined') }} - {{ ExpenseDayLabel::forProfile($user->created_at) }}
        </flux:text>
        <div class="flex justify-between gap-2">
            <flux:button type="button" icon="arrow-right-start-on-rectangle" variant="filled" class="w-full" wire:click="logout">{{ __('Logout') }}</flux:button>
            <flux:modal.trigger name="edit-profile">
                <flux:button variant="filled" icon="pencil" class="w-full">{{ __('Edit Profile') }}</flux:button>
            </flux:modal.trigger>
        </div>
    </flux:card>

    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
        <flux:callout variant="warning" icon="exclamation-circle" class="w-90 md:w-auto max-w-2xl mx-auto">
            <flux:callout.heading>{{ __('Email Verification') }}</flux:callout.heading>

            <flux:callout.text>{{ __('Your email address is unverified. Click the button below to send a verification link to your email address.') }}</flux:callout.text>

            <x-slot name="actions">
                <flux:button icon="envelope" variant="primary" color="yellow">{{ __('Verify Email') }}</flux:button>
            </x-slot>

            @if (session('status') === 'verification-link-sent')
                <flux:callout.text>
                    {{ __('A new verification link has been sent to your email address.') }}
                </flux:callout.text>
            @endif
        </flux:callout>
    @endif

    <flux:card class="mx-auto m-4 w-90 md:w-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <flux:heading size="lg" class="font-bold capitalize text-2xl dark:text-ink-invert">
            {{ __('Settings') }}
        </flux:heading>

        <div class="flex justify-between items-center gap-2 mt-6">
            <div class="flex items-center justify-left gap-2">
                <flux:icon.globe-alt />
                <flux:heading size="md" class="text-lg font-bold capitalize">
                    {{ __('Display language') }}
                </flux:heading>
            </div>

            <x-display-language-toggle />
        </div>

        <flux:separator class="my-4" />

        <div class="flex justify-between items-center gap-2 mt-4">
            <div class="flex items-center justify-left gap-2">
                <flux:icon.sun />
                <flux:heading size="md" class="text-lg font-bold capitalize">
                    {{ __('Appearance') }}
                </flux:heading>
            </div>

            <x-appearance-toggle />
        </div>

    </flux:card>

    <flux:card class="mx-auto m-4 w-90 md:w-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <flux:heading size="lg" class="font-bold capitalize text-2xl dark:text-ink-invert">
            {{ __('Password') }}
        </flux:heading>
        <flux:subheading class="mt-2 mb-4">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </flux:subheading>

        <flux:modal.trigger name="update-password">
            <flux:button variant="filled">{{ __('Update Password') }}</flux:button>
        </flux:modal.trigger>
    </flux:card>

    <flux:card class="mx-auto m-4 w-90 md:w-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <flux:heading size="lg" class="font-bold capitalize text-2xl dark:text-ink-invert">
            {{ __('Danger Zone') }}
        </flux:heading>
        <flux:subheading class="mt-2 mb-4">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </flux:subheading>

        <flux:modal.trigger name="delete-account">
            <flux:button variant="danger" icon="trash">{{ __('Delete Account') }}</flux:button>
        </flux:modal.trigger>
    </flux:card>

    <flux:modal name="update-password" class="w-90 md:w-auto">
        <div class="space-y-6">
            <flux:heading size="lg">{{ __('Update Password') }}</flux:heading>

            <form wire:submit="updatePassword" class="space-y-4">
                @csrf

                <flux:input name="password" type="password" :label="__('Current Password')" wire:model="password" :placeholder="__('Current Password')" required />

                <flux:input
                    name="new_password"
                    type="password"
                    wire:model="new_password"
                    :label="__('New Password')"
                    :placeholder="__('New Password')"
                    required
                />

                <flux:input name="new_password_confirmation" type="password" :label="__('Confirm New Password')" wire:model="new_password_confirmation" :placeholder="__('Confirm New Password')" required />

                <div class="flex justify-between gap-2">
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button class="w-full" variant="primary" color="blue" type="submit">{{ __('Update') }}</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <flux:modal name="edit-profile" class="w-90 md:w-auto">
        <div class="space-y-6">
            <flux:heading size="lg">{{ __('Edit Profile') }}</flux:heading>

            <form wire:submit="save" class="space-y-4">
                @csrf

                <flux:input name="name" :label="__('Name')" wire:model="name" :placeholder="__('Name')" required />

                <flux:input name="email" type="email" :label="__('Email')" wire:model="email" :placeholder="__('Email')" required />

                <div class="flex justify-between gap-2">
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button class="w-full" variant="primary" color="blue" type="submit">{{ __('Update') }}</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <flux:modal name="delete-account" class="w-90 md:w-auto">
        <div class="space-y-6">
            <flux:heading size="lg">{{ __('Delete Account') }}</flux:heading>

            <form wire:submit="deleteAccount" class="space-y-4">
                @csrf

                <flux:input name="password" type="password" :label="__('Password')" wire:model="password" :placeholder="__('Password')" required />

                <div class="flex justify-between gap-2">
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button class="w-full" variant="danger" type="submit">{{ __('Delete Account') }}</flux:button>
            </form>
        </div>
    </flux:modal>
</div>
