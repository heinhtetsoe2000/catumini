<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-ink dark:text-ink-invert leading-tight flex items-center justify-between">
            Today
            <a href="{{ route('add-expense') }}" class="bg-accent text-white px-4 py-2 text-sm font-medium rounded-md dark:bg-accent-dark dark:text-paper-dark hover:bg-accent-hover dark:hover:bg-accent-dark-hover">
                Add Expense
            </a>
        </h2>
    </x-slot>

    <div>
        <div class="bg-paper-elevated dark:bg-paper-dark-elevated overflow-hidden border-b border-ink/10 dark:border-ink-invert/10">
            <div class="p-6 text-ink dark:text-ink-invert">
                <h1 class="text-4xl text-center font-serif font-bold">
                    {{ number_format($total) }} Ks
                </h1>

                <p class="text-sm text-ink-muted text-center">
                    {{ now()->format('M d, Y') }}
                </p>
            </div>
        </div>

        <div class="mt-4 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @forelse ($expenses as $expense)
                <x-expense-record :expense="$expense" />
            @empty
                <p class="text-sm text-ink-muted my-4 text-center">No expenses yet</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
