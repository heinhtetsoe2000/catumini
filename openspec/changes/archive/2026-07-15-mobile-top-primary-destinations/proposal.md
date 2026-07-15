## Why

On small screens, **Today** and **Monthly** are reachable only through the hamburger menu, which makes the main ledger switch tedious on iPhone Safari. The destinations are already primary peers on desktop; mobile should match that one-tap intent without a bottom tab bar or NativePHP EDGE chrome.

## What Changes

- Show **Today** and **Monthly** as always-visible top-bar primary destinations on mobile (no hamburger to switch between them)
- Mobile: calendar-day / calendar-month icons with glossary `aria-label`s (“Today” / “Monthly”); desktop: keep text-labeled peers
- Replace the hamburger drawer with a compact account control (person/avatar → Profile / Log Out), matching desktop menu contents
- Keep wordmark → **Today**; **Add Expense** remains Today CTA only (not a shell peer)
- Current/highlight state only for exact `home` and `dashboard` routes (Add/Edit/Profile leave both peers unselected)
- No dirty-form leave guards, no bottom navigation, no sidebar
- Decision recorded in ADR 0003

## Capabilities

### New Capabilities

<!-- none — extends existing shell chrome -->

### Modified Capabilities

- `mobile-web-shell`: Always-visible mobile top destinations for Today | Monthly; remove hamburger-only peer access; account via compact menu control; icon vs text presentation by breakpoint; current-state and Add-CTA rules as above

## Impact

- Views: `resources/views/layouts/navigation.blade.php` (primary)
- Layout: authenticated `layouts/app.blade.php` only if shell wrappers need adjustment
- Tests: `tests/Feature/MobileWebShellTest.php` (and related shell assertions)
- Docs: ADR 0003 already accepted; glossary notes in `CONTEXT.md`
- No route, controller, or dependency changes expected
