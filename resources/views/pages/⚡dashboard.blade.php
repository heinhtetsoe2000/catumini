<?php

use Livewire\Component;
use App\Models\Expense;
use App\Services\ExpenseAggregateCache;
use App\Support\ExpenseDayLabel;
use Carbon\Carbon;
use Illuminate\Support\Collection;

new class extends Component
{
    public bool $incomeTracking = false;

    public string $mode = "all";

    public int $monthReceived = 0;

    public int $total = 0;

    public int $average = 0;

    public int $available = 0;

    public string $month;

    public Collection $expenses;

    public Collection $expenseByDays;

    public function mount()
    {
        $this->month = now()->toDateString();
        $this->incomeTracking = (bool) auth()->user()->income_tracking;

        $this->calculateExpenses();
        $this->calculateIncome();
        $this->setExpenses();
    }

    private function setExpenses(): void
    {
        $this->expenses = Expense::ofMonth(Carbon::parse($this->month))->currentUser()->orderBy('created_at', 'desc')->get();
    }

    private function calculateExpenses(): void
    {
        $dayTotals = $this->aggregateCache()->monthDayTotals(
            auth()->user()->id,
            Carbon::parse($this->month)
        );

        /** @var Collection<string, int> $expenses */
        $this->expenseByDays = collect($dayTotals)
            ->sortKeysDesc()
            ->mapWithKeys(fn (int $amount, string $spentOn): array => [
                $spentOn => $amount,
            ]);

        $this->total = (int) $this->expenseByDays->sum();
        $this->average = $this->expenseByDays->count() > 0 ? (int) round($this->expenseByDays->avg()) : 0;
    }

    private function calculateIncome(): void
    {
        if ($this->incomeTracking) {
            $this->monthReceived = $this->aggregateCache()->monthIncomeTotal(auth()->user()->id, Carbon::parse($this->month));

            $this->available = $this->aggregateCache()->available(auth()->user()->id);
        }
    }

    private function aggregateCache(): ExpenseAggregateCache
    {
        return app(ExpenseAggregateCache::class);
    }

    public function isAllMode(): bool
    {
        return $this->mode == "all";
    }

    public function isDaysMode(): bool
    {
        return $this->mode == "days";
    }

    #[Transition(type: 'backward')]
    public function previousMonth()
    {
        $this->month = Carbon::parse($this->month)->subMonth()->toDateString();
        $this->calculateExpenses();
        $this->calculateIncome();
        $this->setExpenses();
    }

    #[Transition(type: 'forward')]
    public function nextMonth()
    {
        $this->month = Carbon::parse($this->month)->addMonth()->toDateString();
        $this->calculateExpenses();
        $this->calculateIncome();
        $this->setExpenses();
    }
};
?>

<div>
    @if ($incomeTracking)
        <flux:card class="mx-auto m-4 w-90 md:w-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-4 text-center">
                <div class="text-left">
                    <flux:text class="text-sm">{{ Carbon::parse($this->month)->format('F') }} {{ __('Income') }}</flux:text>
                    <flux:text class="font-bold" color="green">{{ number_format($monthReceived) }} {{ __('Ks') }}</flux:text>
                </div>
                <div class="text-right">
                    <flux:text class="text-sm">{{ __('Available') }}</flux:text>
                    <flux:text class="font-bold" color="green"><x-available-amount :amount="$available" /></flux:text>
                </div>
            </div>
        </flux:card>
    @endif

    <div class="mx-auto m-4 w-90 md:w-auto max-w-2xl flex items-center justify-between gap-4 mb-4">
        <div>
            <flux:text class="text-center font-bold" wire:transition>
                {{ ExpenseDayLabel::forMonth(Carbon::parse($month)) }}
            </flux:text>
        </div>

        <div class="flex items-center gap-2">
            <flux:button wire:click="previousMonth">
                <flux:icon name="chevron-left" />
            </flux:button>

            <flux:button wire:click="nextMonth" :disabled="Carbon::parse($month)->isToday()" class="{{ Carbon::parse($month)->isToday() ? 'opacity-50 cursor-not-allowed' : '' }}">
                <flux:icon name="chevron-right" />
            </flux:button>
        </div>
    </div>

    <flux:card class="mx-auto m-4 w-90 md:w-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <flux:text class="text-center mb-4">
            {{ ExpenseDayLabel::forMonth(Carbon::parse($month)) }}
        </flux:text>

        <h1 class="text-center text-4xl font-bold mb-4">
            {{ number_format($total) }} {{ __('Ks') }}
        </h1>

        <div class="flex items-center justify-center gap-1">
            <flux:badge color="blue" rounded icon="percent-badge">{{ number_format($average) }} {{ __('Ks') }}</flux:badge>
        </div>
    </flux:card>

    <flux:radio.group wire:model="mode" variant="segmented" class="mx-auto mt-4 w-90 md:w-auto max-w-2xl">
        <flux:radio value="all" label="All" />
        <flux:radio value="days" label="Days" />
    </flux:radio.group>

    <div wire:show="mode == 'days'">
        <div class="mx-auto m-4 w-90 md:w-auto max-w-2xl bg-white dark:bg-black rounded-xl border border-black/10 dark:border-white/10" wire:transition>
            @forelse ($expenseByDays as $spent_on => $amount)
                <x-expense-record :date="$spent_on" :amount="$amount" />

                @if ($spent_on !== $expenseByDays->keys()->last())
                    <flux:separator />
                @endif
            @empty
                <flux:text class="my-4 text-center">{{ __('No expenses this month') }}</flux:text>
            @endforelse
        </div>
    </div>

    <div wire:show="mode == 'all'">
        <div class="mx-auto m-4 w-90 md:w-auto max-w-2xl bg-white dark:bg-black rounded-xl border border-black/10 dark:border-white/10" wire:transition>
            @forelse ($expenses as $index => $expense)
                <x-expense-record :expense="$expense" />

                @if ($index !== count($expenses) - 1)
                <flux:separator />
            @endif
            @empty
                <flux:text class="my-4 text-center">{{ __('No expenses this month') }}</flux:text>
            @endforelse
        </div>
    </div>
</div>
