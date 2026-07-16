<?php

use Livewire\Component;
use App\Models\Expense;
use Illuminate\Support\Collection;

new class extends Component
{
    public string $name = '';

    public int $amount = 0;

    public string $spent_on = '';

    public string $description = '';

    public int $total = 0;

    public Collection $expenses;

    public function mount()
    {
        $this->spent_on = now()->toDateString();
        $this->total = Expense::today()->currentUser()->sum('amount');
        $this->expenses = Expense::today()->currentUser()->orderBy('created_at', 'desc')->get();
    }

    public function save()
    {
        $validated = $this->validate([
            'name' => 'required|max:255',
            'amount' => 'required|numeric|min:0',
            'spent_on' => 'required|date',
            'description' => 'nullable|string',
        ]);

        Expense::create([...$validated, 'user_id' => auth()->id()]);
        $this->modal('add-expense')->close();
        $this->reset('name', 'amount', 'spent_on', 'description');
        $this->total = Expense::today()->currentUser()->sum('amount');
        $this->expenses = Expense::today()->currentUser()->orderBy('created_at', 'desc')->get();
    }
};
?>

<div>
    <div class="flex items-center justify-between gap-4 p-4">
        <flux:heading size="lg" class="font-serif text-ink dark:text-ink-invert">
            Today
        </flux:heading>
        <flux:modal.trigger name="add-expense">
            <flux:button icon="plus" variant="primary" />
        </flux:modal.trigger>
    </div>
    <flux:modal name="add-expense" class="md:w-96">
        <div class="space-y-6">
            <flux:heading size="lg">Add Expense</flux:heading>

            <form wire:submit="save" class="space-y-4">
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
                    <flux:button class="w-full" variant="primary" type="submit">Add Expense</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
    <div>
        <div class="overflow-hidden border-b border-ink/10 bg-paper-elevated dark:border-ink-invert/10 dark:bg-paper-dark-elevated">
            <div class="p-6 text-ink dark:text-ink-invert">
                <h1 class="text-center font-serif text-4xl font-bold">
                    {{ number_format($this->total) }} Ks
                </h1>

                <p class="text-center text-sm text-ink-muted">
                    {{ now()->format('M d, Y') }}
                </p>
            </div>
        </div>

        <div class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8">
            @forelse ($this->expenses as $expense)
                <livewire:expense.edit :expense="$expense" />
            @empty
                <flux:text class="my-4 text-center text-ink-muted">No expenses yet</flux:text>
            @endforelse
        </div>
    </div>
</div>
