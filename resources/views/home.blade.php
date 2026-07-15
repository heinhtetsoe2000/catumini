<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <flux:heading size="lg" class="!font-serif text-ink dark:text-ink-invert">
                Today
            </flux:heading>
            <flux:button
                :href="route('add-expense')"
                variant="primary"
                size="sm"
                icon="plus"
                aria-label="{{ __('Add Expense') }}"
            />
        </div>
    </x-slot>

    <div>
        <div class="overflow-hidden border-b border-ink/10 bg-paper-elevated dark:border-ink-invert/10 dark:bg-paper-dark-elevated">
            <div class="p-6 text-ink dark:text-ink-invert">
                <h1 class="text-center font-serif text-4xl font-bold">
                    {{ number_format($total) }} Ks
                </h1>

                <p class="text-center text-sm text-ink-muted">
                    {{ now()->format('M d, Y') }}
                </p>
            </div>
        </div>

        <div class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8">
            @forelse ($expenses as $expense)
                <x-expense-record :expense="$expense" />
            @empty
                <flux:text class="my-4 text-center text-ink-muted">No expenses yet</flux:text>
            @endforelse
        </div>
    </div>
</x-app-layout>
