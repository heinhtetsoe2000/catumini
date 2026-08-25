<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<flux:card class="mx-auto m-4 w-90 md:w-auto max-w-2xl px-4 sm:px-6 lg:px-8">
    <flux:heading size="lg" class="font-bold capitalize text-2xl dark:text-ink-invert">
        {{ __('Settings') }}
    </flux:heading>

    <livewire:settings.language />

    <flux:separator class="my-4" />

    <livewire:settings.appearance />

    <flux:separator class="my-4" />

    <livewire:settings.income />
</flux:card>
