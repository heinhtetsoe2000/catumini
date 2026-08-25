<nav class="sticky top-0 z-50 border-b border-black/10 bg-white dark:border-white/10 dark:bg-black">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-3">
            <flux:brand :href="route('home')" logo="logo-with-bg.png" wire:navigate/>

            <div class="flex min-w-0 items-center gap-4 sm:gap-8">
                <x-nav-bar.desktop class="hidden sm:flex gap-4" />

                <x-nav-bar.mobile class="flex sm:hidden gap-4" />
            </div>

            <div class="flex items-center">
                <x-nav-profile.desktop class="hidden sm:block" />

                <x-nav-profile.mobile class="flex justify-center sm:hidden" />
            </div>
        </div>
    </div>
</nav>
