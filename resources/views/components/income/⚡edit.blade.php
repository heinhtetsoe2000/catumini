<?php

use Livewire\Component;
use App\Models\Income;

new class extends Component
{
    public Income $income;

    public string $name;

    public int $amount;

    public string $received_on = '';

    public ?string $description = '';

    public function mount(Income $income): void
    {
        $this->name = $income->name;
        $this->amount = $income->amount;
        $this->received_on = $income->received_on->toDateString();
        $this->description = $income->description;
    }

    public function update(): void
    {
        abort_unless($this->income->isOwnedBy(auth()->user()), 403);

        $this->income->update($this->validate([
            'name' => 'required|max:255',
            'amount' => 'required|numeric|min:0',
            'received_on' => 'required|date',
            'description' => 'nullable|string',
        ]));

        $this->modal('edit-income-'.$this->income->id)->close();
    }

    public function openEditModal(): void
    {
        $this->modal('edit-income-'.$this->income->id)->show();
    }

    public function openDeleteModal(): void
    {
        $this->modal('delete-income-'.$this->income->id)->show();
    }

    public function delete(): void
    {
        abort_unless($this->income->isOwnedBy(auth()->user()), 403);

        $this->income->delete();
        $this->modal('delete-income-'.$this->income->id)->close();

        $this->dispatch('deleted');
    }
};
?>

<div>
    <button wire:click="openEditModal" class="w-full flex items-center justify-between gap-2 p-4 data-loading:opacity-50 [&[data-loading]_.icon]:animate-spin">
        <flux:icon.loading class="icon mx-auto not-in-data-loading:hidden" />
        <div class="in-data-loading:hidden">
            <flux:text class="text-left text-lg font-bold truncate max-w-[10rem] sm:max-w-[13rem] text-black dark:text-white">{{ $name }}</flux:text>
            @if ($description)
                <flux:text class="text-left text-sm truncate max-w-[10rem] sm:max-w-[13rem] text-black dark:text-white">{{ $description }}</flux:text>
            @endif
            <flux:text class="text-left text-sm text-black/60 dark:text-white/60">{{ $income->received_on->format('M d, Y') }}</flux:text>
        </div>
        <flux:text class="text-left text-lg font-bold text-black dark:text-white in-data-loading:hidden">{{ number_format($amount) }} {{ __('Ks') }}</flux:text>
    </button>

    <flux:modal name="edit-income-{{ $income->id }}" class="w-90 md:w-auto">
        <div class="space-y-6">
            <flux:heading size="lg">{{ __('Edit Income') }}</flux:heading>

            <form wire:submit="update" class="space-y-4">
                @csrf

                <flux:input name="name" wire:model="name" :placeholder="__('Name')" required />

                <div class="flex items-center justify-between gap-2">
                    <flux:input name="amount" type="number" wire:model="amount" :placeholder="__('Amount (Ks)')" min="0" step="1" required />
                    <flux:input name="received_on" type="date" wire:model="received_on" required />
                </div>

                <flux:textarea name="description" wire:model="description" :placeholder="__('Description')">{{ $this->description }}</flux:textarea>

                <div class="flex justify-between gap-2">
                    <flux:button class="w-full" variant="danger" wire:click="openDeleteModal">{{ __('Delete') }}</flux:button>
                    <flux:button class="w-full" variant="primary" color="zinc" type="submit">{{ __('Update') }}</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <flux:modal name="delete-income-{{ $income->id }}" class="w-90 md:w-auto">
        <div class="space-y-6">
            <flux:heading size="lg">{{ __('Delete') }} "{{ $name }}"</flux:heading>

            <form wire:submit="delete" class="space-y-4">
                @csrf

                <div class="flex justify-between gap-2">
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button class="w-full" variant="danger" type="submit">{{ __('Delete') }}</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
