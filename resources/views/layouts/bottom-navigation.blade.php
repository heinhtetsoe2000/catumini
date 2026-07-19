<div class="flex sm:hidden fixed bottom-[50px] left-0 right-0 mx-auto m-4 px-4 bg-paper-elevated dark:bg-paper-dark-elevated border border-ink/10 dark:border-ink-invert/10 rounded-full w-fit items-center justify-center">
    <flux:navbar class="flex justify-center">
        <flux:navbar.item
            :href="route('home')"
            icon="home"
            :current="request()->routeIs('home')"
            aria-label="{{ __('Home') }}"
        />
        <flux:navbar.item
            :href="route('dashboard')"
            icon="calendar-days"
            :current="request()->routeIs('dashboard')"
            aria-label="{{ __('History') }}"
        />
        <flux:navbar.item
            :href="route('profile')"
            icon="user"
            :current="request()->routeIs('profile')"
            aria-label="{{ __('Profile') }}"
        />
    </flux:navbar>
</div>
