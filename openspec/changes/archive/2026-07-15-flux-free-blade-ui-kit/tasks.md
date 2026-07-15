## 1. Dependencies and assets

- [x] 1.1 Install `livewire/livewire` and `livewire/flux` (Free) per current Flux Laravel docs
- [x] 1.2 Wire Flux/Livewire styles and scripts into app and guest layouts (Vite as required)
- [x] 1.3 Resolve Alpine bootstrap so Livewire’s Alpine and `resources/js/app.js` do not double-start
- [x] 1.4 Map ledger ink tokens (paper/ink/accent, dark pairs) onto Flux theme configuration
- [x] 1.5 Confirm ADR 0002 and CONTEXT.md pointer remain accurate (no drift from grilled decisions)

## 2. Shell (layouts and navigation)

- [x] 2.1 Convert `layouts/app.blade.php` and `layouts/guest.blade.php` to Flux-ready shells while preserving structure
- [x] 2.2 Rebuild `layouts/navigation.blade.php` with Flux primitives: Today | Monthly peers, profile menu, mobile toggle; no Add top-nav item; wordmark stays
- [x] 2.3 Smoke-check light and dark shell chrome for stock-Flux color leakage

## 3. In-scope pages

- [x] 3.1 Update Login gate (`welcome` / `/`) primary CTA and any controls to Flux, keep ledger ink + entrance motion
- [x] 3.2 Update login (and other guest-layout consumers touched by shared chrome) controls to Flux where practical without expanding into forgot/reset redesign
- [x] 3.3 Convert Today (`home`) list chrome and Add Expense CTA to Flux; keep classic form posts elsewhere
- [x] 3.4 Convert Monthly (`dashboard`) chrome to Flux
- [x] 3.5 Convert add-expense and edit-expense forms to Flux inputs/buttons/textarea; keep native date input and POST routes
- [x] 3.6 Convert profile edit (and its partials/modals) to Flux controls

## 4. Cleanup

- [x] 4.1 Grep for unused Breeze UI components (`primary-button`, `text-input`, `nav-link`, `dropdown`, etc.) and delete those unreferenced by in-scope views
- [x] 4.2 Leave guest-auth-only leftovers only if still required by out-of-scope pages; note any intentional leftovers

## 5. Verification

- [x] 5.1 Update or add Pest feature tests for Flux shell IA (Today/Monthly peers, no Add top-nav, Profile via menu) and unchanged Today form-post behavior
- [x] 5.2 Update fragile view assertions that targeted Breeze-only markup/classes
- [x] 5.3 Run Pint on dirty PHP and `php artisan test --compact` for affected tests
- [x] 5.4 Manually verify Login gate, login, Today, Monthly, add/edit, profile in light + dark (or document deferral if no device)
