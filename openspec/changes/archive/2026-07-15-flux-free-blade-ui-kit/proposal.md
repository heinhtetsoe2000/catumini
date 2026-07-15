## Why

The authenticated app still builds UI from hand-rolled Breeze Blade primitives. That kit is inconsistent to maintain and does not match the intended component layer for the ledger ink product. Adopting Livewire Flux Free as the Blade UI kit (themed to ledger ink) gives a coherent control set without rewriting the app as Livewire-first.

## What Changes

- Install `livewire/livewire` and `livewire/flux` (Free only; Pro deferred)
- Theme Flux to the existing **ledger ink** visual system (ADR 0001); do not adopt Flux’s stock look
- Keep controller → Blade + classic form posts; Livewire is primarily Flux’s runtime
- Swap in-scope UI to Flux components while preserving app/guest layout structure and IA (Today, Monthly, Profile, Login gate)
- Roll out shell-first (layouts, nav, shared controls), then Login gate, Today/Monthly, add/edit Expense, profile
- Remove unused Breeze UI Blade components as in-scope views stop referencing them
- Leave out-of-scope guest auth extras (forgot/reset/confirm/verify) on leftovers until a later pass unless they break the shared guest layout
- Record stack decision in `docs/adr/0002-flux-free-blade-ui-kit.md` (already drafted)

## Capabilities

### New Capabilities

- `flux-blade-ui`: Flux Free as the Blade component kit for in-scope surfaces; Livewire installed as runtime; no Livewire component rewrite of ledger flows

### Modified Capabilities

- `ledger-ink-theme`: Theme tokens continue to own brand look; Flux components MUST be styled/configured to ledger ink rather than stock Flux aesthetics
- `mobile-web-shell`: Shell chrome uses Flux primitives inside the same app layout / top-nav IA (no navigation redesign; no EDGE)

## Impact

- Dependencies: `livewire/livewire`, `livewire/flux` (Free)
- Layouts/nav: `layouts/app.blade.php`, `layouts/guest.blade.php`, `layouts/navigation.blade.php`
- In-scope views: Login gate, login (guest layout consumer as needed for consistency), Today, Monthly, add/edit expense, profile
- Assets: Livewire/Flux CSS/JS wiring; Alpine may need deduplication vs manual `Alpine.start()` in `resources/js/app.js`
- Cleanup: delete unused Breeze UI components under `resources/views/components/`
- Docs: ADR 0002 + CONTEXT cross-reference (already started)
- Tests: update view assertions that target Breeze markup; add/adjust smoke coverage for shell + in-scope pages still rendering and navigating correctly
