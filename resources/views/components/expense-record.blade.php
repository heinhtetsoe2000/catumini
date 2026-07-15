@props(['expense' => null, 'name' => null, 'amount' => null, 'description' => null])

@php
    $displayName = $expense?->name ?? $name;
    $displayAmount = $expense?->amount ?? $amount;
    $displayDescription = $expense?->description ?? $description;
@endphp

<div class="p-4 flex flex-wrap items-center justify-between gap-2 bg-paper-elevated dark:bg-paper-dark-elevated overflow-hidden border border-ink/10 dark:border-ink-invert/10 my-2 rounded-lg">
    <div class="flex flex-col min-w-0">
        <span class="text-sm text-ink font-bold dark:text-ink-invert">{{ $displayName }}</span>
        @if ($displayDescription)
            <span class="text-sm text-ink-muted dark:text-ink-soft">{{ $displayDescription }}</span>
        @endif
    </div>
    <div class="flex items-center gap-3">
        <span class="text-sm font-bold text-accent dark:text-accent-dark">{{ number_format($displayAmount) }} Ks</span>
        @if ($expense)
            <a href="{{ route('expenses.edit', $expense) }}" class="text-sm text-accent dark:text-accent-dark">Edit</a>
            <form action="{{ route('expenses.destroy', $expense) }}" method="post" onsubmit="return confirm('Delete this expense?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-red-600 dark:text-red-400">Delete</button>
            </form>
        @endif
    </div>
</div>
