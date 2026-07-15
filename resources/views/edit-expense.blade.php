<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-ink dark:text-ink-invert leading-tight">
            Edit Expense
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-paper-elevated dark:bg-paper-dark-elevated overflow-hidden border border-ink/10 dark:border-ink-invert/10 sm:rounded-lg">
                <div class="p-6 text-ink dark:text-ink-invert">
                    <form action="{{ route('expenses.update', $expense) }}" method="post">
                        @csrf
                        @method('PUT')
                        <input class="w-full p-2 border border-ink/20 rounded-md mb-2 dark:border-ink-invert/20 dark:bg-paper-dark dark:text-ink-invert focus:border-accent focus:ring-accent" type="text" name="name" value="{{ old('name', $expense->name) }}" placeholder="Expense name" required>
                        <input class="w-full p-2 border border-ink/20 rounded-md mb-2 dark:border-ink-invert/20 dark:bg-paper-dark dark:text-ink-invert focus:border-accent focus:ring-accent" type="number" name="amount" value="{{ old('amount', $expense->amount) }}" placeholder="Amount (Ks)" min="0" step="1" required>
                        <input class="w-full p-2 border border-ink/20 rounded-md mb-2 dark:border-ink-invert/20 dark:bg-paper-dark dark:text-ink-invert focus:border-accent focus:ring-accent" type="date" name="spent_on" value="{{ old('spent_on', $expense->spent_on->toDateString()) }}" required>
                        <textarea class="w-full p-2 border border-ink/20 rounded-md mb-2 dark:border-ink-invert/20 dark:bg-paper-dark dark:text-ink-invert focus:border-accent focus:ring-accent" name="description" placeholder="Description">{{ old('description', $expense->description) }}</textarea>
                        @if ($errors->any())
                            <ul class="mb-2 text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                        <div class="flex justify-between gap-2">
                            <a href="{{ route('home') }}" class="px-4 py-2 bg-ink-muted text-white rounded-md hover:bg-ink">Back</a>
                            <button class="px-4 py-2 bg-accent text-white rounded-md dark:bg-accent-dark dark:text-paper-dark hover:bg-accent-hover dark:hover:bg-accent-dark-hover" type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
