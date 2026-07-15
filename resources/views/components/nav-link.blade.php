@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-accent dark:border-accent-dark text-sm font-medium leading-5 text-ink dark:text-ink-invert focus:outline-none focus:border-accent-hover transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-ink-muted dark:text-ink-soft hover:text-ink dark:hover:text-ink-invert hover:border-ink/20 dark:hover:border-ink-invert/20 focus:outline-none focus:text-ink dark:focus:text-ink-invert focus:border-ink/20 dark:focus:border-ink-invert/20 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
