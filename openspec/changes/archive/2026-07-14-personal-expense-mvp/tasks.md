## 1. Domain docs & timezone

- [x] 1.1 Write `CONTEXT.md` glossary from grill decisions (Expense, Spend date / `spent_on`, Today, Monthly, Ks, Owner, closed registration, mobile-web MVP)
- [x] 1.2 Set application timezone to `Asia/Yangon` (`config/app.php` / `.env.example` as appropriate)

## 2. Expense data model

- [x] 2.1 Add migration for `spent_on` date on expenses; backfill existing rows from `created_at` date
- [x] 2.2 Update `Expense` fillable, casts, factory; rewrite `today` / `monthly` scopes to use `spent_on`
- [x] 2.3 Ensure `user_id` is always set on create and required for ownership checks

## 3. Expense ledger UI & routes

- [x] 3.1 Add edit form + update route/controller action (owner-only); include spend date on create and edit
- [x] 3.2 Add hard-delete route/action with confirmation (owner-only)
- [x] 3.3 Wire edit/delete actions from expense record component
- [x] 3.4 Display amounts and totals with `Ks` label across Today, Monthly, and forms
- [x] 3.5 Update Monthly dashboard grouping/totals to use `spent_on`; guard empty-month averages/totals

## 4. Closed registration

- [x] 4.1 Disable public register GET/POST routes and remove/hide register links from guest UI
- [x] 4.2 Ensure DatabaseSeeder (or docs path) creates the intentional owner account for local/Cloud bootstrap
- [x] 4.3 Keep login and password reset working

## 5. Mobile web shell

- [x] 5.1 Remove NativePHP EDGE components (`native:top-bar`, `native:bottom-nav`) from Today / expense views
- [x] 5.2 Confirm Breeze web nav reaches Today, Add, Monthly, Profile without native chrome
- [x] 5.3 Leave `GetDeviceAction` / `DeviceType` unused for now (do not delete unless blocking)

## 6. Tests

- [x] 6.1 Feature tests: create with default and backdated `spent_on`; Today/Monthly filter by spend date under Yangon
- [x] 6.2 Feature tests: owner can update and hard-delete; non-owner cannot
- [x] 6.3 Feature tests: registration unavailable; login still succeeds for seeded user
- [x] 6.4 Feature/view assertion: Today page has no EDGE native components
- [x] 6.5 Run affected Pest tests (`php artisan test --compact` for new/updated files)

## 7. Polish & ship gate

- [x] 7.1 Run Pint on dirty PHP (`vendor/bin/pint --dirty --format agent`)
- [ ] 7.2 Manual checklist: deploy to Laravel Cloud, dogfood create/edit/delete/Today/Monthly on iPhone Safari
