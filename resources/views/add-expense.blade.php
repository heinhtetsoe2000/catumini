<x-app-layout>
    <x-slot name="header">
        <flux:heading size="lg" class="!font-serif text-ink dark:text-ink-invert">
            Add Expense
        </flux:heading>
    </x-slot>

    <div>
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden border border-ink/10 bg-paper-elevated sm:rounded-lg dark:border-ink-invert/10 dark:bg-paper-dark-elevated">
                <div class="p-6">
                    <form action="{{ route('home.store') }}" method="post" class="space-y-4">
                        @csrf

                        <flux:input name="name" value="{{ old('name') }}" label="Expense name" placeholder="Expense name" required />
                        <flux:error name="name" />

                        <flux:input name="amount" type="number" value="{{ old('amount') }}" label="Amount (Ks)" placeholder="Amount (Ks)" min="0" step="1" required />
                        <flux:error name="amount" />

                        <flux:input name="spent_on" type="date" value="{{ old('spent_on', now()->toDateString()) }}" label="Spend date" required />
                        <flux:error name="spent_on" />

                        <flux:textarea name="description" label="Description" placeholder="Description">{{ old('description') }}</flux:textarea>
                        <flux:error name="description" />

                        <div class="flex justify-between gap-2">
                            <flux:button :href="route('home')" variant="filled">Back</flux:button>
                            <flux:button type="submit" variant="primary">Add Expense</flux:button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
