<?php

use Livewire\Component;
use App\Models\Expense;

new class extends Component
{
    public Expense $expense;

    public string $name;

    public int $amount;

    public string $spent_on = '';

    public ?string $description = '';

    public function mount(Expense $expense)
    {
        $this->name = $expense->name;
        $this->amount = $expense->amount;
        $this->spent_on = $expense->spent_on->toDateString();
        $this->description = $expense->description;
    }

    public function update()
    {
        $this->expense->update($this->validate([
            'name' => 'required|max:255',
            'amount' => 'required|numeric|min:0',
            'spent_on' => 'required|date',
            'description' => 'nullable|string',
        ]));

        $this->modal('edit-expense-' . $this->expense->id)->close();
    }

    public function openEditModal()
    {
        $this->modal('edit-expense-' . $this->expense->id)->show();
    }

    public function openDeleteModal()
    {
        $this->modal('delete-expense-' . $this->expense->id)->show();
    }

    public function delete()
    {
        $this->expense->delete();
        $this->modal('delete-expense-' . $this->expense->id)->close();
    }
};
?>

<div>
    <div class="my-2 flex flex-wrap items-center justify-between gap-2 overflow-hidden rounded-lg border border-ink/10 bg-paper-elevated p-4 dark:border-ink-invert/10 dark:bg-paper-dark-elevated">
        <div class="flex min-w-0 flex-col">
            <span class="text-sm font-bold text-ink dark:text-ink-invert">{{ $name }}</span>
            @if ($description)
                <span class="text-sm text-ink-muted dark:text-ink-soft">{{ $description }}</span>
            @endif
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm font-bold text-accent">{{ number_format($amount) }} Ks</span>

            <flux:dropdown>
                <flux:button icon="ellipsis-horizontal" variant="ghost" />

                <flux:menu>
                    <flux:menu.item icon="pencil" wire:click="openEditModal">Edit</flux:menu.item>
                    <flux:menu.item variant="danger" icon="trash" wire:click="openDeleteModal">Delete</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </div>
    </div>

    <flux:modal name="edit-expense-{{ $expense->id }}" class="md:w-96">
        <div class="space-y-6">
            <flux:heading size="lg">Edit Expense</flux:heading>

            <form wire:submit="update" class="space-y-4">
                @csrf

                <flux:input name="name" wire:model="name" placeholder="Expense name" required />
                @error('name')
                    <flux:error>{{ $message }}</flux:error>
                @enderror

                <flux:input name="amount" type="number" wire:model="amount" placeholder="Amount (Ks)" min="0" step="1" required />
                @error('amount')
                    <flux:error>{{ $message }}</flux:error>
                @enderror

                <flux:input name="spent_on" type="date" wire:model="spent_on" required />
                @error('spent_on')
                    <flux:error>{{ $message }}</flux:error>
                @enderror

                <flux:textarea name="description" wire:model="description" placeholder="Description">{{ $this->description }}</flux:textarea>
                @error('description')
                    <flux:error>{{ $message }}</flux:error>
                @enderror

                <div class="flex justify-between gap-2">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button class="w-full" variant="primary" type="submit">Update</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <flux:modal name="delete-expense-{{ $expense->id }}" class="md:w-96">
        <div class="space-y-6">
            <flux:heading size="lg">Delete "{{ $name }}"</flux:heading>

            <form wire:submit="delete" class="space-y-4">
                @csrf

                <div class="flex justify-between gap-2">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button class="w-full" variant="danger" type="submit">Delete</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
