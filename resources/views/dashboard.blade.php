@php
    use App\Support\ExpenseDayLabel;
@endphp
<x-app-layout>
    <div>
        @if ($incomeTracking)
            <flux:card class="mx-auto m-4 w-90 md:w-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between gap-4 text-center">
                    <div class="text-left">
                        <flux:text class="text-sm">{{ __('Total Income') }}</flux:text>
                        <flux:text class="font-bold" color="green">{{ number_format($monthReceived) }} {{ __('Ks') }}</flux:text>
                    </div>
                    <div class="text-right">
                        <flux:text class="text-sm">{{ __('Available') }}</flux:text>
                        <flux:text class="font-bold" color="green"><x-available-amount :amount="$available" /></flux:text>
                    </div>
                </div>
            </flux:card>
        @endif

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
                <flux:text class="my-4 text-center">{{ __('No expenses this month') }}</flux:text>
            @endforelse
        </div>
    </div>
</x-app-layout>
