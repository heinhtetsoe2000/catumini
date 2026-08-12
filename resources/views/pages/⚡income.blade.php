<?php

use Livewire\Component;
use App\Models\Income;
use App\Services\ExpenseAggregateCache;
use App\Support\ExpenseDayLabel;
use Illuminate\Support\Collection;

new class extends Component
{
    public string $name = '';

    public int $amount;

    public string $received_on = '';

    public string $description = '';

    public int $monthReceived = 0;

    public int $monthSpent = 0;

    public int $monthNet = 0;

    public int $available = 0;

    public Collection $incomes;

    public function mount(): void
    {
        if (! auth()->user()->income_tracking) {
            $this->redirectRoute('home');

            return;
        }

        $this->received_on = now()->toDateString();
        $this->refreshTotals();
        $this->incomes = Income::query()
            ->currentUser()
            ->orderByDesc('received_on')
            ->orderByDesc('created_at')
            ->get();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|max:255',
            'amount' => 'required|numeric|min:0',
            'received_on' => 'required|date',
            'description' => 'nullable|string',
        ]);

        Income::create([...$validated, 'user_id' => auth()->id()]);

        $this->modal('add-income')->close();
        $this->reset('name', 'amount', 'received_on', 'description');
        $this->received_on = now()->toDateString();
        $this->refreshTotals();
        $this->incomes = Income::query()
            ->currentUser()
            ->orderByDesc('received_on')
            ->orderByDesc('created_at')
            ->get();
    }

    public function handleDeleted(): void
    {
        $this->refreshTotals();
    }

    private function refreshTotals(): void
    {
        $cache = $this->aggregateCache();
        $ownerId = auth()->id();
        $month = now();

        $this->monthReceived = $cache->monthIncomeTotal($ownerId, $month);
        $this->monthSpent = (int) collect($cache->monthDayTotals($ownerId, $month))->sum();
        $this->monthNet = $this->monthReceived - $this->monthSpent;
        $this->available = $cache->available($ownerId);
    }

    private function aggregateCache(): ExpenseAggregateCache
    {
        return app(ExpenseAggregateCache::class);
    }
};
?>

<div>
    <flux:card class="mx-auto m-4 w-90 md:w-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4 text-center">
            <div class="text-left">
                <flux:text class="text-sm">{{ __('Total') }}</flux:text>
                <flux:text class="font-bold" color="green">{{ number_format($monthReceived) }} {{ __('Ks') }}</flux:text>
            </div>
            <div>
                <flux:modal.trigger name="add-income">
                    <flux:button icon="plus" class="w-full" variant="primary" color="zinc">{{ __('Add Income') }}</flux:button>
                </flux:modal.trigger>
            </div>
        </div>
    </flux:card>

    <flux:card class="mx-auto m-4 w-90 md:w-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-4">
            <flux:text class="text-sm">{{ __('Available') }}</flux:text>
            <h1 class="text-4xl font-bold">
                <x-available-amount :amount="$available" />
            </h1>
        </div>
    </flux:card>

    <div class="mx-auto m-4 w-90 md:w-auto max-w-2xl bg-white dark:bg-black rounded-xl border border-black/10 dark:border-white/10">
        @forelse ($incomes as $index => $income)
            <livewire:income.edit wire:key="income-{{ $income->id }}" :income="$income" @deleted="handleDeleted" />

            @if ($index !== count($incomes) - 1)
                <flux:separator />
            @endif
        @empty
            <flux:text class="my-4 text-center text-black dark:text-white">{{ __('No income recorded yet') }}</flux:text>
        @endforelse
    </div>

    <flux:modal name="add-income" class="w-90 md:w-auto">
        <div class="space-y-6">
            <flux:heading size="lg">{{ __('Add Income') }}</flux:heading>

            <form wire:submit="save" class="space-y-4">
                @csrf

                <flux:input name="name" wire:model="name" :placeholder="__('Name')" required />

                <div class="flex items-center justify-between gap-2">
                    <flux:input name="amount" type="number" wire:model="amount" :placeholder="__('Amount (Ks)')" min="0" step="1" required />
                    <flux:input name="received_on" type="date" wire:model="received_on" required />
                </div>

                <flux:textarea name="description" wire:model="description" :placeholder="__('Description')">{{ $this->description }}</flux:textarea>

                <div class="flex justify-between gap-2">
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button class="w-full" variant="primary" color="zinc" type="submit">{{ __('Add') }}</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
