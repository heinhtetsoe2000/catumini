## Why

The current expense journal can create and list expenses, but it is not yet a shippable personal MVP for iPhone dogfooding: there is no edit/delete, Today/Monthly use `created_at` instead of spend date, timezone is UTC, registration is open, NativePHP EDGE chrome mixes into the web UI, and there is no hosted URL for phone use. Lock the product to a mobile-web personal ledger and close those gaps before Laravel Cloud deploy.

## What Changes

- Add full expense correction: **edit** and **hard delete** for the authenticated owner's expenses
- Add a dedicated **spend date** field (default today, backdatable); drive Today and Monthly filters from it, not `created_at`
- Display amounts as integers with the **Ks** currency label; keep single-currency, no decimals, no categories
- Set application timezone to **Asia/Yangon** for Today / Monthly boundaries
- **Close public registration**; MVP audience is a single personal account (login + seed/create owner only)
- **Strip / ignore NativePHP EDGE** components for the web MVP; keep Breeze web navigation suitable for iPhone Safari
- Write domain glossary (`CONTEXT.md`) from grill decisions so language stays consistent
- Deploy target remains **Laravel Cloud** (ops, not app code); NativePHP / App Store deferred until Mac access

## Capabilities

### New Capabilities

- `expense-ledger`: Create, edit, hard-delete personal expenses; spend date; integer amounts labeled Ks; Today and Monthly views scoped to the owner in Asia/Yangon
- `closed-registration`: Disable public sign-up so only the intentional personal account can authenticate
- `mobile-web-shell`: Web-only UI for MVP — remove EDGE chrome from the expense flow; keep navigable Today / Add / Monthly / Profile on phone

### Modified Capabilities

- (none — no existing specs under `openspec/specs/`)

## Impact

- Models/migrations: `Expense` (+ spend date), scopes, factories/seeders
- Controllers/routes: expense update/destroy; Today (`home`) and Monthly (`dashboard`) filter logic; registration routes/views disabled or gated
- Views: add/edit forms, expense record actions, Ks labeling, remove `native:*` from home; empty-month dashboard avg safety
- Config: `APP_TIMEZONE` / `config/app.php` → `Asia/Yangon`
- Docs: new `CONTEXT.md` glossary
- Tests: feature coverage for CRUD, spend-date filtering, closed registration
- Out of scope for this change: NativePHP builds, income tracking, categories/budgets, soft deletes, multi-user households, App Store / Play Store
