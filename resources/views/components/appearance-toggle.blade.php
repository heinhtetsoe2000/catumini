@props([
    'current' => null,
])

@php
    use App\Enums\Appearance;

    $currentAppearance = $current ?? Appearance::tryFrom($resolvedAppearance ?? null) ?? Appearance::System;
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex rounded-full border border-black/10 p-1 dark:border-white/10']) }} role="group" aria-label="{{ __('Appearance') }}">
    @foreach (Appearance::cases() as $appearance)
        <form method="POST" action="{{ route('preferences.appearance') }}" class="inline">
            @csrf
            <input type="hidden" name="appearance" value="{{ $appearance->value }}">
            <button
                type="submit"
                onclick="window.Flux?.applyAppearance('{{ $appearance->value }}')"
                @class([
                    'rounded-full px-3 py-1 text-sm font-medium transition',
                    'bg-black text-white dark:bg-white dark:text-black' => $currentAppearance === $appearance,
                    'hover:text-ink dark:text-ink-soft dark:hover:text-ink-invert' => $currentAppearance !== $appearance,
                ])
            >
                {{ $appearance->label() }}
            </button>
        </form>
    @endforeach
</div>
