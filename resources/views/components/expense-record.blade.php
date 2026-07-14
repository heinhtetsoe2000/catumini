@props(['expense' => null, 'name' => null, 'amount' => null, 'description' => null])

@php
    $displayName = $expense?->name ?? $name;
    $displayAmount = $expense?->amount ?? $amount;
    $displayDescription = $expense?->description ?? $description;
@endphp

<div class="p-4 flex flex-wrap items-center justify-between gap-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm my-2 rounded-lg">
    <div class="flex flex-col min-w-0">
        <span class="text-sm text-black font-bold dark:text-white">{{ $displayName }}</span>
        @if ($displayDescription)
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $displayDescription }}</span>
        @endif
    </div>
    <div class="flex items-center gap-3">
        <span class="text-sm font-bold text-green-500 dark:text-green-400">{{ number_format($displayAmount) }} Ks</span>
        @if ($expense)
            <a href="{{ route('expenses.edit', $expense) }}" class="text-sm text-blue-600 dark:text-blue-400">Edit</a>
            <form action="{{ route('expenses.destroy', $expense) }}" method="post" onsubmit="return confirm('Delete this expense?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-red-600 dark:text-red-400">Delete</button>
            </form>
        @endif
    </div>
</div>
