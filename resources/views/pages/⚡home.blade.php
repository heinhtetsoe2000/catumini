<?php

use Livewire\Component;
use App\Models\Expense;
use App\Services\ExpenseAggregateCache;
use App\Support\ExpenseDayLabel;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Livewire\Attributes\Transition;

new class extends Component
{
    public string $name = '';

    public int $amount;

    public string $spent_on = '';

    public string $description = '';

    public int $total = 0;

    public int $average = 0;

    public int $difference = 0;

    public bool $showAISummary = false;

    public Collection $expenses;

    public function mount()
    {
        $this->spent_on = now()->toDateString();
        $this->calculateTotal();
        $this->calculateAverage();
        $this->calculateDifference();
        $this->showAISummary();
        $this->expenses = Expense::today()->currentUser()->orderBy('created_at', 'desc')->get();
    }

    public function showAISummary(): void
    {
        $this->showAISummary = $this->total != 0 && $this->difference != 0;
    }

    #[Transition(type: 'backward')]
    public function previousDay()
    {
        $this->spent_on = Carbon::parse($this->spent_on)->subDay()->toDateString();
        $this->calculateTotal();
        $this->calculateDifference();
        $this->showAISummary();
        $this->expenses = Expense::ofDay(Carbon::parse($this->spent_on))->currentUser()->orderBy('created_at', 'desc')->get();
    }

    #[Transition(type: 'forward')]
    public function nextDay()
    {
        $this->spent_on = Carbon::parse($this->spent_on)->addDay()->toDateString();
        $this->calculateTotal();
        $this->calculateDifference();
        $this->showAISummary();
        $this->expenses = Expense::ofDay(Carbon::parse($this->spent_on))->currentUser()->orderBy('created_at', 'desc')->get();
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
        $this->spent_on = now()->toDateString();
        $this->calculateTotal();
        $this->calculateAverage();
        $this->calculateDifference();
        $this->showAISummary();
        $this->expenses = Expense::today()->currentUser()->orderBy('created_at', 'desc')->get();
    }

    public function handleDeleted(): void
    {
        $this->calculateTotal();
        $this->calculateAverage();
        $this->calculateDifference();
        $this->showAISummary();
    }

    private function calculateTotal(): void
    {
        $this->total = $this->aggregateCache()->dayTotal(
            auth()->id(),
            Carbon::parse($this->spent_on)
        );
    }

    private function calculateAverage(): void
    {
        $dailyTotals = collect($this->aggregateCache()->monthDayTotals(
            auth()->id(),
            now()
        ));

        $this->average = $dailyTotals->isEmpty()
            ? 0
            : (int) round($dailyTotals->avg());
    }

    private function calculateDifference(): void
    {
        $this->difference = $this->total - $this->average;
    }

    private function aggregateCache(): ExpenseAggregateCache
    {
        return app(ExpenseAggregateCache::class);
    }
};
?>

<div>
    <flux:card class="mx-auto m-4 w-90 md:w-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4">
            <flux:button variant="ghost" wire:click="previousDay">
                <flux:icon name="chevron-left" />
            </flux:button>

            <div>
                <flux:text class="text-center font-bold text-ink-muted" wire:transition>
                    {{ ExpenseDayLabel::forHeader(Carbon::parse($spent_on)) }}
                </flux:text>
                <h1 class="text-center text-4xl font-bold" wire:transition>
                    {{ number_format($total) }} {{ __('Ks') }}
                </h1>
            </div>

            <flux:button variant="ghost" wire:click="nextDay" :disabled="Carbon::parse($spent_on)->isToday()" class="{{ Carbon::parse($spent_on)->isToday() ? 'opacity-50 cursor-not-allowed' : '' }}">
                <flux:icon name="chevron-right" />
            </flux:button>
        </div>

        <flux:separator class="my-4" />

        <flux:modal.trigger name="add-expense">
            <flux:button icon="plus" class="w-full" variant="primary" color="zinc">{{ __('Add Expense') }}</flux:button>
        </flux:modal.trigger>
    </flux:card>

    <flux:callout wire:show="showAISummary" icon="sparkles" color="purple" class="w-90 md:w-auto mx-auto max-w-2xl" x-transition.duration.500ms>
        <flux:callout.heading>{{ __('AI Summary') }}</flux:callout.heading>

        <flux:callout.text>
            {{ __('You have spent :amount Ks :direction than the average.', [
                'amount' => number_format(abs($difference)),
                'direction' => $difference > 0 ? __('more') : __('less'),
            ]) }}
        </flux:callout.text>
    </flux:callout>

    <div class="mx-auto m-4 w-90 md:w-auto max-w-2xl bg-white dark:bg-black rounded-xl border border-black/10 dark:border-white/10" wire:transition>
        @forelse ($expenses as $index => $expense)
            <livewire:expense.edit wire:key="expense-{{ $expense->id }}" :expense="$expense" @deleted="handleDeleted" x-transition.duration.500ms />

            @if ($index !== count($expenses) - 1)
                <flux:separator />
            @endif
        @empty
            <flux:text class="my-4 text-center text-black dark:text-white">{{ __('No expenses this day') }}</flux:text>
        @endforelse
    </div>

    <flux:modal name="add-expense" class="w-90 md:w-auto">
        <div class="space-y-6">
            <flux:heading size="lg">{{ __('Add Expense') }}</flux:heading>

            <form wire:submit="save" class="space-y-4">
                @csrf

                <flux:input name="name" wire:model="name" :placeholder="__('Name')" required />

                <div class="flex items-center justify-between gap-2">
                    <flux:input name="amount" type="number" wire:model="amount" :placeholder="__('Amount (Ks)')" min="0" step="1" required />
                    <flux:input name="spent_on" type="date" wire:model="spent_on" required />
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
