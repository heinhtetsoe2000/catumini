<div class="flex sm:hidden fixed bottom-[30px] left-0 right-0 mx-auto m-4 px-4 bg-white dark:bg-black border border-black/10 dark:border-white/10 rounded-full w-fit items-center justify-center">
    <flux:navbar class="flex justify-center">
        <flux:navbar.item
            :href="route('home')"
            icon="home"
            wire:navigate
            :current="request()->routeIs('home')"
            aria-label="{{ __('Home') }}"
        />
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
</div>
