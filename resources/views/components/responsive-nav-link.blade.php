@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-accent dark:border-accent-dark text-start text-base font-medium text-accent dark:text-accent-dark bg-accent-muted/60 dark:bg-accent/10 focus:outline-none focus:text-accent-hover dark:focus:text-accent-dark-hover focus:bg-accent-muted dark:focus:bg-accent/20 focus:border-accent-hover dark:focus:border-accent-dark-hover transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-ink-muted dark:text-ink-soft hover:text-ink dark:hover:text-ink-invert hover:bg-paper dark:hover:bg-paper-dark hover:border-ink/20 dark:hover:border-ink-invert/20 focus:outline-none focus:text-ink dark:focus:text-ink-invert focus:bg-paper dark:focus:bg-paper-dark focus:border-ink/20 dark:focus:border-ink-invert/20 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
