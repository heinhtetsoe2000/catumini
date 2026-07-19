## Why

The mobile bottom navigation pill uses `dark:bg-ink-invert`, which is a light text token (`#e8eaed`), not a dark surface. In dark mode the dock stays bright while Flux icons switch to light strokes, so icons become hard to see and the bar looks out of place next to surfaces like the top nav.

## What Changes

- Update the bottom navigation pill background (and optional border) to use the same elevated surface tokens as the top nav: `bg-paper-elevated` / `dark:bg-paper-dark-elevated`
- Keep `ink-invert` for text/icon contrast roles only, not as a background
- Out of scope: home expense list `dark:bg-ink-invert` (same antipattern, separate cleanup if desired)
- Out of scope: reconciling `mobile-web-shell` (which forbids bottom nav) with the dock that already ships in the app

## Capabilities

### New Capabilities
- (none)

### Modified Capabilities
- `ledger-ink-theme`: Require elevated shell chrome (including the existing mobile bottom dock) to use paper elevated / paper-dark-elevated surface tokens in light and dark appearance, not text tokens such as `ink-invert` as backgrounds

## Impact

- `resources/views/layouts/bottom-navigation.blade.php` — class token swap on the dock container
- Visual only; no route, Livewire, or API changes
- Relies on existing theme tokens in `resources/css/app.css` and Flux dark mode via `@fluxAppearance`
