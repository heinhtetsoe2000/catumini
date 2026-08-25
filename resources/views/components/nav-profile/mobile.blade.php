<flux:navbar {{ $attributes }}>
    <flux:navbar.item
        :href="route('profile')"
        icon="user"
        wire:navigate
        :current="request()->routeIs('profile')"
        aria-label="{{ __('Profile') }}"
    />
</flux:navbar>
