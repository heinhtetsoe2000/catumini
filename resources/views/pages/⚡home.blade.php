<?php

use Livewire\Component;
use App\Models\Expense;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

new class extends Component
{
    public string $name = '';

    public int $amount;

    public string $spent_on = '';

    public string $description = '';

    public int $total = 0;

    public int $average = 0;

    public int $difference = 0;

    public Collection $expenses;

    public function mount()
    {
        $this->spent_on = now()->toDateString();
        $this->calculateTotal();
        $this->calculateAverage();
        $this->calculateDifference();
        $this->expenses = Expense::today()->currentUser()->orderBy('created_at', 'desc')->get();
    }

    public function previousDay()
    {
        $this->spent_on = Carbon::parse($this->spent_on)->subDay()->toDateString();
        $this->calculateTotal();
        $this->calculateDifference();
        $this->expenses = Expense::ofDay(Carbon::parse($this->spent_on))->currentUser()->orderBy('created_at', 'desc')->get();
    }

    public function nextDay()
    {
        $this->spent_on = Carbon::parse($this->spent_on)->addDay()->toDateString();
        $this->calculateTotal();
        $this->calculateDifference();
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
        $this->expenses = Expense::today()->currentUser()->orderBy('created_at', 'desc')->get();
    }

    private function calculateTotal(): void
    {
        $this->total = Expense::ofDay(Carbon::parse($this->spent_on))->currentUser()->sum('amount');
    }

    private function calculateAverage(): void
    {
        $dailyTotals = Expense::query()
            ->monthly()
            ->currentUser()
            ->get()
            ->groupBy(fn (Expense $expense): string => $expense->spent_on->format('D M d'))
            ->map(fn ($group) => $group->sum('amount'));

        $this->average = $dailyTotals->isEmpty()
            ? 0
            : (int) round($dailyTotals->avg());
    }

    private function calculateDifference(): void
    {
        $this->difference = $this->total - $this->average;
    }
};
?>

<div>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <flux:heading size="lg" class="font-bold text-2xl dark:text-ink-invert">
                    {{ Carbon::parse($spent_on)->isToday() ? __('Today') : Carbon::parse($spent_on)->format('M d, Y') }}
                </flux:heading>
                <flux:button variant="ghost" wire:click="previousDay">
                    <flux:icon name="chevron-left" />
                </flux:button>
                <flux:button variant="ghost" wire:click="nextDay" :disabled="Carbon::parse($spent_on)->isToday()" class="{{ Carbon::parse($spent_on)->isToday() ? 'opacity-50 cursor-not-allowed' : '' }}">
                    <flux:icon name="chevron-right" />
                </flux:button>
            </div>
            <flux:modal.trigger name="add-expense">
                <flux:button icon="plus" class="rounded-full" />
            </flux:modal.trigger>
        </div>
    </div>

    <flux:card class="mx-auto m-4 w-xs sm:w-96 max-w-7xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-center text-4xl font-bold">
            {{ number_format($total) }} Ks
        </h1>
    </flux:card>

    <flux:callout icon="information-circle" class="w-xs sm:w-96 mx-auto max-w-7xl">
        <flux:callout.heading>{{ __('Summary') }}</flux:callout.heading>

        @if ($total === 0)
            <flux:callout.text>
                You haven't spent any.
            </flux:callout.text>
        @else
            <flux:callout.text>
                You have spent {{ number_format(abs($difference)) }} Ks {{ $difference > 0 ? 'more' : 'less' }} than the average.
            </flux:callout.text>
        @endif
    </flux:callout>

    <div class="mx-auto m-4 w-xs sm:w-96 max-w-7xl bg-paper-elevated dark:bg-paper-dark-elevated rounded-xl border border-ink/10 dark:border-ink-invert/10">
        @forelse ($expenses as $index => $expense)
            <livewire:expense.edit wire:key="expense-{{ $expense->id }}" :expense="$expense" />

            @if ($index !== count($expenses) - 1)
                <flux:separator />
            @endif
        @empty
            <flux:text class="my-4 text-center text-ink-muted">No expenses this month</flux:text>
        @endforelse
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

                <div class="flex items-center justify-between gap-2">
                    <flux:input name="amount" type="number" wire:model="amount" placeholder="Amount (Ks)" min="0" required />
                    @error('amount')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror

                    <flux:input name="spent_on" type="date" wire:model="spent_on" required />
                    @error('spent_on')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </div>

                <flux:textarea name="description" wire:model="description" placeholder="Description">{{ $this->description }}</flux:textarea>
                @error('description')
                    <flux:error>{{ $message }}</flux:error>
                @enderror

                <div class="flex justify-between gap-2">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button class="w-full" variant="primary" color="blue" type="submit">Add Expense</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
