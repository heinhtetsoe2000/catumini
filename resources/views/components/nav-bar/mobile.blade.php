<flux:navbar {{ $attributes }}>
    <flux:navbar.item
        :href="route('home')"
        icon="home"
        wire:navigate
        :current="request()->routeIs('home')"
        aria-label="{{ __('Home') }}"
    />
    @if (Auth::user()->income_tracking)
        <flux:navbar.item
            :href="route('income')"
            icon="banknotes"
            wire:navigate
            :current="request()->routeIs('income')"
            aria-label="{{ __('Income') }}"
        />
    @endif
    <flux:navbar.item
        :href="route('dashboard')"
        icon="calendar-days"
        wire:navigate
        :current="request()->routeIs('dashboard')"
        aria-label="{{ __('History') }}"
    />
</flux:navbar>
