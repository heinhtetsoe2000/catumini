<nav class="sticky hidden sm:block top-0 z-50 border-b border-black/10 bg-white dark:border-white/10 dark:bg-black">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-3">
            <flux:brand :href="route('home')" logo="logo-with-bg.png" wire:navigate/>

            <div class="flex min-w-0 items-center gap-4 sm:gap-8">
                <flux:navbar class="hidden sm:flex gap-4">
                    <flux:navbar.item :href="route('home')" :current="request()->routeIs('home')" icon="home" wire:navigate>
                        {{ __('Home') }}
                    </flux:navbar.item>
                    @if (Auth::user()->income_tracking)
                        <flux:navbar.item :href="route('income')" :current="request()->routeIs('income')" icon="arrow-down-on-square" wire:navigate>
                            {{ __('Income') }}
                        </flux:navbar.item>
                    @endif
                    <flux:navbar.item :href="route('dashboard')" :current="request()->routeIs('dashboard')" icon="calendar-days" wire:navigate>
                        {{ __('History') }}
                    </flux:navbar.item>
                </flux:navbar>
            </div>

            <div class="flex items-center">
                <x-nav-profile.desktop class="hidden sm:block" />

                <x-nav-profile.mobile class="flex justify-center sm:hidden" />
            </div>
        </div>
    </div>
</nav>
