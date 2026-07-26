## Why

Auto-increment integer primary keys on **Owner** (`users`) and **Expense** rows block future NativePHP offline capture and public multi-user launch: the device must mint an **Expense identity** before any server round-trip, and **Owner identity** must not be enumerable in verify-email URLs. Decisions are locked in `docs/adr/0005-uuid-v7-domain-identities.md`.

## What Changes

- **BREAKING:** Replace integer PKs on `users` and `expenses` with UUID v7; `expenses.user_id` becomes a required FK to `users.id`
- **BREAKING:** In-place migration backfills existing rows; deploy truncates sessions (force re-login) and flushes cache
- Add `HasUuids` (v7) on `User` and `Expense`; remove integer casts/coercions on IDs throughout app code
- Update aggregate cache keys to use UUID Owner ids (`owner:{uuid}:day:…`)
- Laravel-ready only: web/server generates v7 on create today; NativePHP client generation and sync endpoints deferred
- Add Pest coverage for migration path, model ID generation, FK integrity, and cache key shape

## Capabilities

### New Capabilities

- `uuid-domain-identities`: UUID v7 client-authoritative primary keys on Owner and Expense, in-place migration with session/cache cutover, and Laravel-side ID generation

### Modified Capabilities

- `expense-ledger`: Every Expense MUST belong to an Owner (required FK); Expense identity is assigned at creation and persists
- `owner-provisioning`: Owner identity is a UUID v7 assigned at provision time

## Impact

- Database: new in-place migration on `users`, `expenses`; FK + NOT NULL on `expenses.user_id`; `sessions` truncated on deploy
- Models: `User`, `Expense` — `HasUuids`, non-incrementing string keys; drop `user_id` integer cast on Expense
- Services: `ExpenseAggregateCache` — Owner id type string/UUID in keys and method signatures
- Controllers/views/tests: remove `(int)` casts on `auth()->id()` / `$user->id`; update factories/seeders
- Auth: verify-email URLs use UUID user id (existing route shape, new id format)
- Cache: full flush on migration deploy; old integer-keyed entries discarded
- Docs: ADR 0005 and CONTEXT.md glossary already updated
- Out of scope: NativePHP sync API, client-side v7 generator, framework tables (`jobs`, etc.)
