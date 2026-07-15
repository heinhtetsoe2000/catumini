## Context

The personal expense ledger MVP is Blade + Alpine + Breeze UI, themed with **ledger ink** (ADR 0001). Domain and IA are stable: Login gate, Today, Monthly, add/edit Expense, Profile; navigation peers Today | Monthly + profile menu; Add expense only via Today CTA. Grilling locked: Flux Free on Blade controllers, themed to ledger ink, shell-first rollout, same shell structure, delete unused Breeze UI, ADR 0002 for the stack choice.

## Goals / Non-Goals

**Goals:**

- Install Livewire + Flux Free and wire assets correctly for Blade layouts
- Map ledger-ink tokens onto Flux theming (primary/accent, paper/ink surfaces, dark mode via system preference)
- Convert in-scope shell and pages to Flux components without changing routes, controllers, or form POST behavior
- Remove unused Breeze UI components once unreferenced by in-scope views
- Keep existing product behavior and tests green; update UI-sensitive assertions

**Non-Goals:**

- Livewire/Volt rewrite of ledger or auth flows
- Flux Pro / custom paid components
- Adopting Flux’s default visual aesthetic
- Restructuring nav IA or introducing NativePHP EDGE chrome
- Full restyle of out-of-scope guest auth pages (forgot/reset/confirm/verify) beyond what guest layout inheritance forces
- Domain/model/API changes

## Decisions

1. **Blade + Flux, not Livewire components**  
   Keep controller → Blade views and classic forms. Flux `<flux:*>` components replace Breeze primitives. Livewire is a dependency for Flux’s runtime, not a signal to migrate page logic into Livewire classes.  
   *Alternatives rejected:* Livewire components for ledger flows; full Volt rewrite.

2. **Theme Flux to ledger ink**  
   Configure Flux appearance (CSS variables / Flux theme tokens / Tailwind) so primary actions use teal-verdigris, surfaces use paper/ink pairs, and titles retain serif wordmark/header treatment from ADR 0001. Stock Flux look is not acceptable.  
   *Alternatives rejected:* Adopt Flux defaults; hybrid “Flux chrome + ledger brand only on gate.”

3. **Free edition; native date input**  
   Use Flux Free inputs/buttons/menus/layout primitives. Keep native `<input type="date">` (or Flux input type=date if Free supports styling wrappers) for spend date. Upgrade to Pro only if a concrete Free gap appears.  
   *Alternatives rejected:* Buy Pro now for datepicker/menus alone.

4. **Shell-first rollout**  
   Order: install + asset wiring → app/guest layouts + navigation + shared controls → Login gate → Today/Monthly → add/edit Expense → profile → delete unused Breeze components.  
   *Alternatives rejected:* Vertical slice; big bang before shell theming works.

5. **Same structure / IA**  
   Preserve `x-app-layout` / `x-guest-layout` (or equivalent layout components), top nav with Today | Monthly, profile dropdown, mobile hamburger. Swap internals to Flux; do not introduce sidebar or bottom-nav.  
   *Alternatives rejected:* Flux-native shell redesign.

6. **Replace and remove Breeze UI**  
   Delete Breeze UI Blade components when no longer referenced by in-scope surfaces. Do not wrap Flux behind `<x-primary-button>` forever. Guest-auth-only leftovers may remain until that pass.  
   *Alternatives rejected:* Thin wrappers; permanent dual stack.

7. **Alpine / JS**  
   Resolve duplicate Alpine bootstrap: Livewire ships Alpine; remove or guard manual `Alpine.start()` in `resources/js/app.js` per Livewire/Flux install docs so nav `x-data` and Flux interactivity do not double-bind.

8. **Docs**  
   ADR 0002 already captures stack decision; keep CONTEXT.md free of implementation detail (pointer only).

### Alternatives considered (change-level)

- Keep polishing Breeze components under ledger ink → rejected (inconsistent kit, more hand-rolled surface area)
- Flux Pro first → rejected (no proven Free gaps)
- Livewire-first rewrite → rejected (CRUD pages do not need it for MVP)

## Risks / Trade-offs

- **[Risk]** Flux Free + Tailwind version/setup mismatch → **Mitigation:** Follow current Flux install docs for Laravel 12; fix Vite/Tailwind config before page churn
- **[Risk]** Dual Alpine breaks mobile nav → **Mitigation:** Deduplicate Alpine as part of install shell step; manual smoke on hamburger + profile dropdown
- **[Risk]** Flux stock colors leak past theme → **Mitigation:** Theme tokens early; visual check light + dark on Login gate and Today
- **[Risk]** View tests assert Breeze-specific classes/markup → **Mitigation:** Prefer behavior/copy/route assertions; update fragile CSS selectors
- **[Trade-off]** Guest auth leftovers may look older until later → Acceptable per scoped grill; guest layout Flux chrome should still improve shared header
- **[Trade-off]** Livewire in composer without Livewire pages → Documented in ADR 0002 so future readers do not “fix” by rewriting everything to Livewire

## Migration Plan

- Composer require Livewire + Flux Free; publish/configure per docs; build frontend assets
- Convert shell then pages; deploy with normal app release (no DB migration)
- Rollback: revert dependency + view/asset commits; restore Breeze components from git if needed

## Open Questions

- None blocking. Exact Flux theme API (CSS vars vs config file) left to implementer following Flux docs while matching ADR 0001 tokens already in `resources/css/app.css`.
