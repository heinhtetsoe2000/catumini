<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-ink dark:text-ink-invert leading-tight">
            {{ __('Monthly') }}
        </h2>
    </x-slot>

    <div>
        <div class="bg-paper-elevated dark:bg-paper-dark-elevated overflow-hidden border-b border-ink/10 dark:border-ink-invert/10 sm:rounded-lg">
            <div class="p-6 text-ink dark:text-ink-invert">
                <p class="text-sm text-ink-muted text-center">
                    {{ now()->format('M Y') }}
                </p>
                <h1 class="text-3xl text-center font-serif font-bold flex justify-center items-center">
                    {{ number_format($total) }} Ks
                </h1>
                <p class="text-sm text-accent dark:text-accent-dark text-center">
                    Avg: {{ number_format($average) }} Ks
                </p>
            </div>
        </div>

        <div class="mt-4 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @forelse ($expenses as $date => $amount)
                <x-expense-record :name="$date" :amount="$amount" />
            @empty
                <p class="text-sm text-ink-muted my-4 text-center">No expenses this month</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
