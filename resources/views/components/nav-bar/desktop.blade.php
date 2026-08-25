<flux:navbar {{ $attributes }}>
    <flux:navbar.item :href="route('home')" :current="request()->routeIs('home')" wire:navigate>
        {{ __('Home') }}
    </flux:navbar.item>
    @if (Auth::user()->income_tracking)
        <flux:navbar.item :href="route('income')" :current="request()->routeIs('income')" wire:navigate>
            {{ __('Income') }}
        </flux:navbar.item>
    @endif
    <flux:navbar.item :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
        {{ __('History') }}
    </flux:navbar.item>
</flux:navbar>
