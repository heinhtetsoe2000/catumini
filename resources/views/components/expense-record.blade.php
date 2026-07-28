@props(['expense' => null, 'name' => null, 'amount' => null, 'description' => null, 'date' => null])

@php
    use App\Support\ExpenseDayLabel;

    $displayName = $expense?->name
        ?? ($date !== null ? ExpenseDayLabel::forDateString($date) : ($name ?? ''));
    $displayAmount = $expense?->amount ?? $amount;
    $displayDescription = $expense?->description ?? $description;
@endphp

<div class="my-2 flex flex-wrap items-center justify-between gap-2 overflow-hidden rounded-lg border border-black/10 bg-white p-4 dark:border-white/10 dark:bg-black">
    <div class="flex min-w-0 flex-col">
        <span class="text-lg font-bold text-black dark:text-white">{{ $displayName }}</span>
        @if ($displayDescription)
            <span class="text-sm text-black dark:text-white">{{ $displayDescription }}</span>
        @endif
    </div>
    <div class="flex items-center gap-3">
        <span class="text-lg font-bold text-black dark:text-white">{{ number_format($displayAmount) }} Ks</span>
    </div>
</div>
