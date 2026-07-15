<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-accent dark:bg-accent-dark border border-transparent rounded-md font-semibold text-xs text-white dark:text-paper-dark uppercase tracking-widest hover:bg-accent-hover dark:hover:bg-accent-dark-hover focus:bg-accent-hover dark:focus:bg-accent-dark-hover active:bg-accent dark:active:bg-accent-dark focus:outline-none focus:ring-2 focus:ring-accent dark:focus:ring-accent-dark focus:ring-offset-2 dark:focus:ring-offset-paper-dark transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
