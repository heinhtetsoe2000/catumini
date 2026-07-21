<x-app-layout>
    <div>
        <flux:card class="mx-auto m-4 w-90 md:w-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <flux:text class="text-center text-sm text-ink-muted">
                {{ now()->format('M Y') }}
            </flux:text>

            <h1 class="text-center text-4xl font-bold">
                {{ number_format($total) }} Ks
            </h1>

            <div class="flex items-center justify-center gap-1">
                <flux:icon.percent-badge variant="micro" class="text-blue-600 dark:text-blue-500" />

                <span class="text-sm text-blue-600 dark:text-blue-500">{{ number_format($average) }} Ks</span>
            </div>
        </flux:card>

        <div class="mx-auto mt-4 w-90 md:w-auto max-w-7xl">
            @forelse ($expenses as $date => $amount)
                <x-expense-record :name="$date" :amount="$amount" />
            @empty
                <flux:text class="my-4 text-center text-ink-muted">No expenses this month</flux:text>
            @endforelse
        </div>
    </div>
</x-app-layout>
