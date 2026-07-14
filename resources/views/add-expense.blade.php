<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-blue-800 dark:text-blue-200 leading-tight">
            Add Expense
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('home.store') }}" method="post">
                        @csrf
                        <input class="w-full p-2 border border-gray-300 rounded-md mb-2 dark:border-gray-600 dark:bg-gray-800 dark:text-white" type="text" name="name" value="{{ old('name') }}" placeholder="Expense name" required>
                        <input class="w-full p-2 border border-gray-300 rounded-md mb-2 dark:border-gray-600 dark:bg-gray-800 dark:text-white" type="number" name="amount" value="{{ old('amount') }}" placeholder="Amount (Ks)" min="0" step="1" required>
                        <input class="w-full p-2 border border-gray-300 rounded-md mb-2 dark:border-gray-600 dark:bg-gray-800 dark:text-white" type="date" name="spent_on" value="{{ old('spent_on', now()->toDateString()) }}" required>
                        <textarea class="w-full p-2 border border-gray-300 rounded-md mb-2 dark:border-gray-600 dark:bg-gray-800 dark:text-white" name="description" placeholder="Description">{{ old('description') }}</textarea>
                        @if ($errors->any())
                            <ul class="mb-2 text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                        <div class="flex justify-between">
                            <a href="{{ route('home') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md dark:bg-gray-600 dark:text-white hover:bg-gray-600 dark:hover:bg-gray-700">Back</a>
                            <button class="px-4 py-2 bg-blue-500 text-white rounded-md dark:bg-blue-600 dark:text-white" type="submit">Add Expense</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
