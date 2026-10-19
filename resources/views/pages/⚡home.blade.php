<?php

use App\Actions\Expense\GetDailyExpenseTotalAmountByMonthAction;
use App\Actions\Expense\GetExpensesByDayAction;
use App\Actions\Expense\GetExpenseTotalAmountByDayAction;
use App\Actions\Expense\StoreExpenseAction;
use Livewire\Component;
use App\Models\Expense;
use App\Models\User;
use App\Services\ExpenseAggregateCache;
use App\Support\ExpenseCategoryAppearance;
use App\Support\ExpenseDayLabel;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Transition;

new class extends Component
{
    public string $name = '';

    public int $amount;

    public string $spent_on = '';

    public string $description = '';

    public ?string $category_id = null;

    public function mount() {
        $this->spent_on = now()->toDateString();
    }

    #[Computed]
    public function categories(): EloquentCollection
    {
        return $this->user->expenseCategories()->orderBy('name')->get();
    }

    #[Computed(persist: true)]
    public function user(): User
    {
        return Auth::user();
    }

    #[Computed(persist: true)]
    public function incomeTracking()
    {
        return (bool) $this->user->income_tracking;
    }

    #[Computed(persist: true)]
    public function expenses()
    {
        return (new GetExpensesByDayAction)->execute(Carbon::parse($this->spent_on));
    }

    #[Computed(persist: true)]
    public function total()
    {
        return (new GetExpenseTotalAmountByDayAction)->execute(Carbon::parse($this->spent_on));
    }

    #[Computed(persist: true)]
    public function average()
    {
        $dailyTotals = collect((new GetDailyExpenseTotalAmountByMonthAction)->execute(Carbon::parse($this->spent_on)));

        return $dailyTotals->isEmpty() ? 0 : (int) round($dailyTotals->avg());
    }

    #[Computed(persist: true)]
    public function difference()
    {
        return $this->total - $this->average;
    }

    #[Computed(persist: true)]
    public function available()
    {
        return $this->aggregateCache()->available($this->user->id);
    }

    #[Computed(persist: true)]
    public function showAISummary()
    {
        return $this->total != 0 && $this->difference != 0;
    }

    #[Transition(type: 'backward')]
    public function previousDay()
    {
        $this->spent_on = Carbon::parse($this->spent_on)->subDay()->toDateString();
        $this->updateData();
    }

    #[Transition(type: 'forward')]
    public function nextDay()
    {
        $this->spent_on = Carbon::parse($this->spent_on)->addDay()->toDateString();
        $this->updateData();
    }

    public function save(StoreExpenseAction $action) {
        $validated = $this->validate([
            'name' => 'required|max:255',
            'amount' => 'required|numeric|min:0',
            'spent_on' => 'required|date',
            'description' => 'nullable|string',
            'category_id' => ['nullable', 'uuid', Rule::exists('expense_categories', 'id')->where('user_id', Auth::id())],
        ]);

        $action->execute($validated);

        $this->modal('add-expense')->close();
        $this->reset('name', 'amount', 'spent_on', 'description', 'category_id');
        $this->spent_on = now()->toDateString();
        $this->updateData();
    }

    public function handleDeleted(): void
    {
        $this->updateData();
    }

    #[On('expense-updated')]
    public function handleUpdated(): void
    {
        $this->updateData();
    }

    private function updateData()
    {
        unset($this->total); 
        unset($this->average);
        unset($this->difference);
        unset($this->available);
        unset($this->showAISummary);
        unset($this->expenses);
    }

    private function aggregateCache(): ExpenseAggregateCache
    {
        return app(ExpenseAggregateCache::class);
    }
};
?>

<div>
    @if ($this->incomeTracking)
        <flux:card class="mx-auto m-4 w-90 md:w-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-4 text-center">
                <div class="text-left">
                    <flux:text class="text-sm">{{ __('Available') }}</flux:text>
                    <flux:text class="font-bold" color="green"><x-available-amount :amount="$this->available" /></flux:text>
                </div>
                <div>
                    <flux:modal.trigger name="add-expense">
                        <flux:button icon="plus" variant="primary" color="zinc">{{ __('Add Expense') }}</flux:button>
                    </flux:modal.trigger>
                </div>
            </div>
        </flux:card>
    @endif

    <div class="mx-auto m-4 w-90 md:w-auto max-w-2xl flex items-center justify-between gap-4 mb-4">
        <div>
            <flux:text class="text-center font-bold" wire:transition>
                {{ ExpenseDayLabel::forHeader(Carbon::parse($spent_on)) }}
            </flux:text>
        </div>

        <div class="flex items-center gap-2">
            <flux:button wire:click="previousDay">
                <flux:icon name="chevron-left" />
            </flux:button>

            <flux:button wire:click="nextDay" :disabled="Carbon::parse($spent_on)->isToday()" class="{{ Carbon::parse($spent_on)->isToday() ? 'opacity-50 cursor-not-allowed' : '' }}">
                <flux:icon name="chevron-right" />
            </flux:button>
        </div>
    </div>

    <flux:card class="mx-auto m-4 w-90 md:w-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-center text-4xl font-bold my-4" wire:transition>
            {{ number_format($this->total) }} {{ __('Ks') }}
        </h1>

        @if (!$this->incomeTracking)

            <flux:separator class="my-4" />
        
            <flux:modal.trigger name="add-expense">
                <flux:button icon="plus" class="w-full" variant="primary" color="zinc">{{ __('Add Expense') }}</flux:button>
            </flux:modal.trigger>
        @endif
    </flux:card>

    <flux:callout wire:show="$this->showAISummary" icon="sparkles" color="purple" class="w-90 md:w-auto mx-auto max-w-2xl" x-transition.duration.500ms>
        <flux:callout.heading>{{ __('Summary') }}</flux:callout.heading>

        <flux:callout.text>
            {{ __('You have spent :amount Ks :direction than the average.', [
                'amount' => number_format(abs($this->difference)),
                'direction' => $this->difference > 0 ? __('more') : __('less'),
            ]) }}
        </flux:callout.text>
    </flux:callout>

    <div class="mx-auto m-4 w-90 md:w-auto max-w-2xl bg-white dark:bg-black rounded-xl border border-black/10 dark:border-white/10" wire:transition>
        @forelse ($this->expenses as $index => $expense)
            <livewire:expense.edit wire:key="expense-{{ $expense->id }}" :expense="$expense" @deleted="handleDeleted" x-transition.duration.500ms />

            @if (!$loop->last)
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

                <div class="flex items-center justify-between gap-2">
                    <flux:input name="name" wire:model="name" :placeholder="__('Name')" required />
                    @php($selectedCategory = $this->categories->firstWhere('id', $category_id))
                    <flux:dropdown position="bottom" align="end" class="w-28 shrink-0">
                        <flux:badge
                            as="button"
                            type="button"
                            size="sm"
                            rounded
                            :color="$selectedCategory && in_array($selectedCategory->color, ExpenseCategoryAppearance::COLORS, true) ? $selectedCategory->color : 'zinc'"
                            :icon="$selectedCategory && in_array($selectedCategory->icon, ExpenseCategoryAppearance::ICONS, true) ? $selectedCategory->icon : null"
                            class="!h-8 w-full justify-between gap-1.5 px-2"
                            aria-label="{{ __('Category') }}: {{ $selectedCategory?->name ?? __('No category') }}"
                            data-expense-category-selected
                        >
                            <span class="min-w-0 truncate">{{ $selectedCategory?->name ?? __('No category') }}</span>
                            <flux:icon.chevron-down class="ms-auto size-3 shrink-0" />
                        </flux:badge>
                        <flux:menu class="w-48 max-h-56 overflow-y-auto">
                            <flux:menu.item
                                as="button"
                                type="button"
                                :aria-label="__('No category')"
                                wire:click="$set('category_id', null)"
                                class="!py-1"
                            >
                                {{ __('No category') }}
                            </flux:menu.item>
                            @foreach ($this->categories as $category)
                                <flux:menu.item
                                    as="button"
                                    type="button"
                                    :aria-label="$category->name"
                                    wire:click="$set('category_id', '{{ $category->id }}')"
                                    data-expense-category-option
                                    class="!py-1"
                                >
                                    <flux:badge
                                        size="sm"
                                        rounded
                                        :color="in_array($category->color, ExpenseCategoryAppearance::COLORS, true) ? $category->color : 'zinc'"
                                        :icon="in_array($category->icon, ExpenseCategoryAppearance::ICONS, true) ? $category->icon : null"
                                    >
                                        {{ $category->name }}
                                    </flux:badge>
                                </flux:menu.item>
                            @endforeach
                        </flux:menu>
                    </flux:dropdown>
                </div>

                <div class="flex items-center justify-between gap-2">
                    <flux:input name="amount" type="number" wire:model="amount" :placeholder="__('Amount (Ks)')" min="0" step="1" required />
                    <flux:input name="spent_on" type="date" wire:model="spent_on" required />
                </div>

                <flux:textarea name="description" wire:model="description" :placeholder="__('Description')">{{ $this->description }}</flux:textarea>

                <div class="flex justify-between gap-2">
                    <flux:button class="w-full" variant="outline" color="zinc" x-on:click="$flux.modal('add-expense').close()">{{ __('Cancel') }}</flux:button>
                    <flux:button class="w-full" variant="primary" color="zinc" type="submit">{{ __('Add') }}</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
