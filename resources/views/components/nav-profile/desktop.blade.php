<div {{ $attributes }}>
    <flux:dropdown position="bottom" align="end">
        <flux:profile :name="ucwords(Auth::user()->name)" avatar:color="auto" />

        <flux:menu>
            <flux:menu.item :href="route('profile')" icon="user">
                {{ __('Profile') }}
            </flux:menu.item>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:menu.item
                    variant="danger"
                    icon="arrow-right-start-on-rectangle"
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
