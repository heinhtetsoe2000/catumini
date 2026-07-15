@props(['expense' => null, 'name' => null, 'amount' => null, 'description' => null])

@php
    $displayName = $expense?->name ?? $name;
    $displayAmount = $expense?->amount ?? $amount;
    $displayDescription = $expense?->description ?? $description;
@endphp

<div class="my-2 flex flex-wrap items-center justify-between gap-2 overflow-hidden rounded-lg border border-ink/10 bg-paper-elevated p-4 dark:border-ink-invert/10 dark:bg-paper-dark-elevated">
    <div class="flex min-w-0 flex-col">
        <span class="text-sm font-bold text-ink dark:text-ink-invert">{{ $displayName }}</span>
        @if ($displayDescription)
            <span class="text-sm text-ink-muted dark:text-ink-soft">{{ $displayDescription }}</span>
        @endif
    </div>
    <div class="flex items-center gap-3">
        <span class="text-sm font-bold text-accent">{{ number_format($displayAmount) }} Ks</span>
        @if ($expense)
            <flux:button :href="route('expenses.edit', $expense)" variant="ghost" size="sm" icon="pencil" />
            <form action="{{ route('expenses.destroy', $expense) }}" method="post" onsubmit="return confirm('Delete this expense?');">
                @csrf
                @method('DELETE')
                <flux:button type="submit" variant="danger" size="sm" icon="trash" />
            </form>
        @endif
    </div>
</div>
