<?php

use App\Models\User;
use Livewire\Component;

new class extends Component
{
    public User $user;

    public function mount()
    {
        $this->user = auth()->user();
    }
};
?>

<div class="flex justify-between items-center gap-2 mt-4">
    <div class="flex items-center justify-left gap-2">
        <flux:icon.banknotes />
        <flux:heading size="md" class="text-lg font-bold capitalize">
            {{ __('Income tracking') }}
        </flux:heading>
    </div>

    <x-income-tracking-toggle :enabled="$user->income_tracking" />
</div>
