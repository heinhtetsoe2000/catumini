@props([
    'enabled' => false,
])

<div {{ $attributes->merge(['class' => 'inline-flex rounded-full border border-black/10 p-1 dark:border-white/10']) }} role="group" aria-label="{{ __('Income tracking') }}">
    <form method="POST" action="{{ route('preferences.income-tracking') }}" class="inline">
        @csrf
        <input type="hidden" name="income_tracking" value="1">
        <button
            type="submit"
            @class([
                'rounded-full px-3 py-1 text-sm font-medium transition',
                'bg-black text-white dark:bg-white dark:text-black' => $enabled,
                'text-dark dark:text-white/50 dark:hover:text-white' => ! $enabled,
            ])
        >
            {{ __('On') }}
        </button>
    </form>
    <form method="POST" action="{{ route('preferences.income-tracking') }}" class="inline">
        @csrf
        <input type="hidden" name="income_tracking" value="0">
        <button
            type="submit"
            @class([
                'rounded-full px-3 py-1 text-sm font-medium transition',
                'bg-black text-white dark:bg-white dark:text-black' => ! $enabled,
                'text-dark dark:text-white/50 dark:hover:text-white' => $enabled,
            ])
        >
            {{ __('Off') }}
        </button>
    </form>
</div>
