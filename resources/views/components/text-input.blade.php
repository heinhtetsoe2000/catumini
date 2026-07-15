@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-ink/20 dark:border-ink-invert/20 dark:bg-paper-dark dark:text-ink-invert focus:border-accent dark:focus:border-accent-dark focus:ring-accent dark:focus:ring-accent-dark rounded-md shadow-sm']) }}>
