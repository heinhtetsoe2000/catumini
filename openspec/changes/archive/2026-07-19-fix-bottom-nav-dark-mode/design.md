## Context

The authenticated shell already defines ledger ink surface tokens in `resources/css/app.css` (`paper` / `paper-elevated` / `paper-dark` / `paper-dark-elevated` for backgrounds; `ink` / `ink-invert` for text). Top navigation correctly uses `bg-paper-elevated dark:bg-paper-dark-elevated`. The mobile bottom dock in `layouts/bottom-navigation.blade.php` incorrectly uses `bg-white dark:bg-ink-invert`, treating a light text token as a dark-mode background. Flux navbar icons follow `.dark` and become light, which fails on that bright pill.

## Goals / Non-Goals

**Goals:**
- Make the mobile bottom dock use the same elevated surface token pair as other shell chrome
- Preserve readable Flux navbar item icons in both light and dark appearance
- Align with the existing ledger ink theme without introducing new CSS variables

**Non-Goals:**
- Redesigning bottom-nav IA or reconciling `mobile-web-shell` (which forbids bottom navigation) with the dock that already exists
- Fixing other `dark:bg-ink-invert` misuse (e.g. home expense list) in this change
- Changing Flux component internals or accent tokens

## Decisions

### 1. Token pair for the dock container
- **Choice:** `bg-paper-elevated` + `dark:bg-paper-dark-elevated`, with optional `border border-ink/10 dark:border-ink-invert/10` to match top nav separation
- **Why:** Matches top nav and other elevated cards; keeps `ink-invert` reserved for text
- **Alternatives considered:**
  - Keep light pill in dark mode and force dark icon classes → fights Flux dark styling; inconsistent with shell
  - Use `dark:bg-paper-dark` → flatter than elevated chrome; less contrast against page `paper-dark`

### 2. Scope limited to the dock wrapper
- **Choice:** Change only the outer container classes in `bottom-navigation.blade.php`; leave `flux:navbar` / `flux:navbar.item` as-is
- **Why:** Icon contrast comes from Flux once the surface is correct; no custom icon color overrides needed

## Risks / Trade-offs

- [Risk] Visual regression if someone preferred the intentional “light floating dock” look → Mitigation: elevated dark surface is the product default elsewhere; easy to revert one class string
- [Risk] Spec drift: `mobile-web-shell` still forbids bottom nav while the app renders it → Mitigation: call out as out of scope; theming fix does not deepen the IA conflict

## Migration Plan

- Deploy is a Blade class change only; no migration or asset rebuild strictly required beyond normal CSS already shipping tokens
- Rollback: restore previous class string on the dock container

## Open Questions

- None for implementation; optional follow-up whether to clean `dark:bg-ink-invert` on the home expense list in a separate change
