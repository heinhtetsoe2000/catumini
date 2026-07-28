@php
    use App\Support\ExpenseDayLabel;
@endphp
<x-app-layout>
    <div>
        <flux:card class="mx-auto m-4 w-90 md:w-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            <flux:text class="text-center mb-4">
                {{ ExpenseDayLabel::forMonth(now()) }}
            </flux:text>

            <h1 class="text-center text-4xl font-bold mb-4">
                {{ number_format($total) }} {{ __('Ks') }}
            </h1>

            <div class="flex items-center justify-center gap-1">
                <flux:badge color="blue" rounded icon="percent-badge">{{ number_format($average) }} {{ __('Ks') }}</flux:badge>
            </div>
        </flux:card>

        <div class="mx-auto mt-4 w-90 md:w-auto max-w-2xl">
            @forelse ($expenses as $date => $amount)
                <x-expense-record :date="$date" :amount="$amount" />
            @empty
                <flux:text class="my-4 text-center text-ink-muted">{{ __('No expenses this month') }}</flux:text>
            @endforelse
        </div>
    </div>
</x-app-layout>
