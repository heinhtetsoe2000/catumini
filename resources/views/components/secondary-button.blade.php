<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-paper-elevated dark:bg-paper-dark-elevated border border-ink/15 dark:border-ink-invert/15 rounded-md font-semibold text-xs text-ink dark:text-ink-invert uppercase tracking-widest shadow-sm hover:bg-paper dark:hover:bg-paper-dark focus:outline-none focus:ring-2 focus:ring-accent dark:focus:ring-accent-dark focus:ring-offset-2 dark:focus:ring-offset-paper-dark disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
