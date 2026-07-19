<nav class="border-b border-ink/10 bg-paper-elevated dark:border-ink-invert/10 dark:bg-paper-dark-elevated">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-3">
            <div class="flex min-w-0 items-center gap-4 sm:gap-8">
                <flux:brand :href="route('home')" logo="/logo.png" name="Wallet" />

                <flux:navbar class="hidden sm:flex">
                    <flux:navbar.item :href="route('home')" :current="request()->routeIs('home')">
                        {{ __('Home') }}
                    </flux:navbar.item>
                    <flux:navbar.item :href="route('dashboard')" :current="request()->routeIs('dashboard')">
                        {{ __('History') }}
                    </flux:navbar.item>
                </flux:navbar>
            </div>

            <div class="flex items-center">
                <div class="hidden sm:block">
                    <flux:dropdown position="bottom" align="end">
                        <flux:button variant="subtle" icon:trailing="chevron-down">
                            {{ Auth::user()->name }}
                        </flux:button>

                        <flux:menu>
                            <flux:menu.item :href="route('profile')">
                                {{ __('Profile') }}
                            </flux:menu.item>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <flux:menu.item
                                    as="button"
                                    type="submit"
                                    class="w-full"
                                >
                                    {{ __('Log Out') }}
                                </flux:menu.item>
                            </form>
                        </flux:menu>
                    </flux:dropdown>
                </div>
            </div>
        </div>
    </div>
</nav>
