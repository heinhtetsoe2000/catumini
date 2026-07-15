<x-app-layout>
    <x-slot name="header">
        <flux:heading size="lg" class="!font-serif text-ink dark:text-ink-invert">
            {{ __('Monthly') }}
        </flux:heading>
    </x-slot>

    <div>
        <div class="overflow-hidden border-b border-ink/10 bg-paper-elevated sm:rounded-lg dark:border-ink-invert/10 dark:bg-paper-dark-elevated">
            <div class="p-6 text-ink dark:text-ink-invert">
                <p class="text-center text-sm text-ink-muted">
                    {{ now()->format('M Y') }}
                </p>
                <h1 class="flex items-center justify-center text-center font-serif text-3xl font-bold">
                    {{ number_format($total) }} Ks
                </h1>
                <p class="text-center text-sm text-accent">
                    Avg: {{ number_format($average) }} Ks
                </p>
            </div>
        </div>

        <div class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8">
            @forelse ($expenses as $date => $amount)
                <x-expense-record :name="$date" :amount="$amount" />
            @empty
                <flux:text class="my-4 text-center text-ink-muted">No expenses this month</flux:text>
            @endforelse
        </div>
    </div>
</x-app-layout>
