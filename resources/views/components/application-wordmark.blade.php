@props([
    'href' => null,
])

@php
    $classes = 'font-semibold tracking-tight text-ink dark:text-ink-invert';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ config('app.name') }}
    </a>
@else
    <span {{ $attributes->merge(['class' => $classes]) }}>
        {{ config('app.name') }}
    </span>
@endif
