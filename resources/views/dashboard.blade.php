<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Monthly') }}
        </h2>
    </x-slot>

    <div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <p class="text-sm text-gray-500 text-center">
                    {{ now()->format('M Y') }}
                </p>
                <h1 class="text-3xl text-center font-bold flex justify-center items-center">
                    {{ number_format($total) }} Ks
                </h1>
                <p class="text-sm text-blue-500 text-center">
                    Avg: {{ number_format($average) }} Ks
                </p>
            </div>
        </div>

        <div class="mt-4 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @forelse ($expenses as $date => $amount)
                <x-expense-record :name="$date" :amount="$amount" />
            @empty
                <p class="text-sm text-gray-500 my-4 text-center">No expenses this month</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
