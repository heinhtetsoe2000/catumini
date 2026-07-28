@props([
    'current' => null,
])

@php
    use App\Enums\DisplayLanguage;

    $currentLanguage = $current ?? ($resolvedDisplayLanguage ?? DisplayLanguage::En);
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex rounded-full border border-black/10 p-1 dark:border-white/10']) }} role="group" aria-label="{{ __('Display language') }}">
    @foreach (DisplayLanguage::cases() as $language)
        <form method="POST" action="{{ route('preferences.display-language') }}" class="inline">
            @csrf
            <input type="hidden" name="display_language" value="{{ $language->value }}">
            <button
                type="submit"
                @class([
                    'rounded-full px-3 py-1 text-sm font-medium transition',
                    'bg-black text-white dark:bg-white dark:text-black' => $currentLanguage === $language,
                    'hover:text-ink dark:text-ink-soft dark:hover:text-ink-invert' => $currentLanguage !== $language,
                ])
            >
                {{ $language->label() }}
            </button>
        </form>
    @endforeach
</div>
