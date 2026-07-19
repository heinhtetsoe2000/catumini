## 1. Dock surface tokens

- [x] 1.1 Update `resources/views/layouts/bottom-navigation.blade.php` dock container from `bg-white dark:bg-ink-invert` to `bg-paper-elevated dark:bg-paper-dark-elevated`
- [x] 1.2 Optionally add `border border-ink/10 dark:border-ink-invert/10` on the dock to match top-nav separation

## 2. Verify

- [x] 2.1 Confirm light mode: dock remains an elevated light surface with readable Flux navbar icons
- [x] 2.2 Confirm dark mode: dock uses paper-dark-elevated (not ink-invert) and Flux icons remain readable
