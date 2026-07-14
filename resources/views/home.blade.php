<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-blue-800 dark:text-blue-200 leading-tight flex items-center justify-between">
            Today
            <a href="{{ route('add-expense') }}" class="bg-blue-500 text-white px-4 py-2 text-sm font-medium rounded-md dark:bg-blue-600 dark:text-white">
                Add Expense
            </a>
        </h2>
    </x-slot>

    <div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h1 class="text-4xl text-center font-bold">
                    {{ number_format($total) }} Ks
                </h1>

                <p class="text-sm text-gray-500 text-center">
                    {{ now()->format('M d, Y') }}
                </p>
            </div>
        </div>

        <div class="mt-4 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @forelse ($expenses as $expense)
                <x-expense-record :expense="$expense" />
            @empty
                <p class="text-sm text-gray-500 my-4 text-center">No expenses yet</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
