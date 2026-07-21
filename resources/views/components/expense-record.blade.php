@props(['expense' => null, 'name' => null, 'amount' => null, 'description' => null])

@php
    $today = now()->format('D M d');
    $yesterday = now()->subDay()->format('D M d');
    $isToday = $name == $today;
    $isYesterday = $name == $yesterday;
    $displayName = $expense?->name ?? ($isToday ? 'Today' : ($isYesterday ? 'Yesterday' : $name));
    $displayAmount = $expense?->amount ?? $amount;
    $displayDescription = $expense?->description ?? $description;
@endphp

<div class="my-2 flex flex-wrap items-center justify-between gap-2 overflow-hidden rounded-lg border border-ink/10 bg-paper-elevated p-4 dark:border-ink-invert/10 dark:bg-paper-dark-elevated">
    <div class="flex min-w-0 flex-col">
        <span class="text-lg font-bold text-ink dark:text-ink-invert">{{ $displayName }}</span>
        @if ($displayDescription)
            <span class="text-sm text-ink-muted dark:text-ink-soft">{{ $displayDescription }}</span>
        @endif
    </div>
    <div class="flex items-center gap-3">
        <span class="text-lg font-bold text-ink">{{ number_format($displayAmount) }} Ks</span>
    </div>
</div>
