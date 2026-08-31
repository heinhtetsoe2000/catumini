<nav class="sticky top-0 z-50 border-b sm:hidden border-black/10 bg-white dark:border-white/10 dark:bg-black">
    <flux:navbar class="flex h-16 items-center px-8 justify-between gap-4">
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
                icon="arrow-down-on-square"
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
            <flux:navbar.item
            :href="route('profile')"
            icon="user"
            wire:navigate
            :current="request()->routeIs('profile')"
            aria-label="{{ __('Profile') }}"
        />
    </flux:navbar>
</nav>
