<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="flex justify-between items-center gap-2 mt-6">
    <div class="flex items-center justify-left gap-2">
        <flux:icon.globe-alt />
        <flux:heading size="md" class="text-lg font-bold capitalize">
            {{ __('Display language') }}
        </flux:heading>
    </div>

    <x-display-language-toggle />
</div>