<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="flex justify-between items-center gap-2 mt-4">
    <div class="flex items-center justify-left gap-2">
        <flux:icon.sun />
        <flux:heading size="md" class="text-lg font-bold capitalize">
            {{ __('Appearance') }}
        </flux:heading>
    </div>

    <x-appearance-toggle />
</div>
