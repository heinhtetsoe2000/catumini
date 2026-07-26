## Context

Personal Expense Ledger (Laravel 12) uses auto-increment bigint PKs on `users` and `expenses`. `expenses.user_id` is a nullable integer with no FK. IDs appear in verify-email URLs, Livewire modal keys, aggregate cache keys (`owner:{int}:day:…`), and `(int)` casts across controllers/services. ADR 0005 locks UUID v7 client-authoritative identities for NativePHP offline sync and public launch. Production data on Laravel Cloud must survive migration.

## Goals / Non-Goals

**Goals:**

- UUID v7 primary keys on `users` and `expenses`; client-authoritative **Expense identity** (same id on device and server after future sync)
- In-place migration: backfill existing rows, swap PKs/FKs, add FK + NOT NULL on `expenses.user_id`
- Deploy cutover: truncate `sessions` (force re-login), flush cache
- Laravel generates v7 on web create via `HasUuids`; update all int-id assumptions in app code and tests
- Pest coverage for migration, model generation, FK integrity, cache keys

**Non-Goals:**

- NativePHP client-side v7 generator or sync API
- Migrating framework tables (`jobs`, `failed_jobs`, `cache`)
- Accepting client-supplied `id` on create in this change (future NativePHP work)
- Preserving active sessions across deploy

## Decisions

### 1. UUID v7 via Laravel `HasUuids`

- **Choice:** `Illuminate\Database\Eloquent\Concerns\HasUuids` on `User` and `Expense` with v7 generation (`uniqueIds()` returning version 7 or framework default for ordered UUIDs in Laravel 12).
- **Rationale:** Native Laravel 12 support; time-ordered; same format for web and future mobile writers.
- **Alternatives:** ULID package (rejected per ADR); UUID v4 (poor index locality); separate `public_id` column (rejected — client-authoritative PK).

### 2. In-place migration sequence

- **Choice:** Single migration (or tightly ordered set):
  1. Add temporary `uuid` columns to `users` and `expenses`
  2. Backfill with `Str::uuid7()` per row; build old-int → new-uuid map for users
  3. Add `user_id_uuid` on `expenses`, backfill from map, resolve any null `user_id` to sole Owner or fail loudly
  4. Drop old PKs/FKs; rename uuid columns to `id`; set PK constraints
  5. Add FK `expenses.user_id` → `users.id` with NOT NULL
  6. Truncate `sessions`; `Cache::flush()`
- **Rationale:** Preserves production **Expense** history; ADR rejects `migrate:fresh`.
- **Alternatives:** Edit base migrations only (rejected — loses prod data); dual-write period (overkill for MVP scale).

### 3. Column type

- **Choice:** Laravel `$table->uuid('id')` — native `uuid` on Postgres (Laravel Cloud), `char(36)` on SQLite (local dev/tests).
- **Rationale:** Framework handles driver differences; string ids in Eloquent either way.
- **Alternatives:** Binary UUID storage (unnecessary complexity).

### 4. Model configuration

- **Choice:** `HasUuids` trait; `$incrementing = false`; `$keyType = 'string'` (trait may set these). Remove `'user_id' => 'integer'` cast on `Expense`. Update `isOwnedBy()` to string comparison without `(int)`.
- **Rationale:** Consistent string/uuid handling; avoids silent int coercion bugs.
- **Alternatives:** Custom boot id generator (rejected — trait is sufficient for web path).

### 5. Aggregate cache Owner id type

- **Choice:** Change `ExpenseAggregateCache` method signatures and key helpers from `int $ownerId` to `string $ownerId`; keys `owner:%s:day:%s` with UUID string.
- **Rationale:** ADR 0004 key shape unchanged except id format; full cache flush on deploy handles transition.
- **Alternatives:** Keep int in cache layer (rejected — inconsistent after migration).

### 6. Session and auth cutover

- **Choice:** Truncate `sessions` in migration; clear `remember_token` on all users optional but acceptable for clean break.
- **Rationale:** Session payloads may embed old integer user id; single Owner re-login is cheap.
- **Alternatives:** Migrate `sessions.user_id` column (rejected — fragile).

## Risks / Trade-offs

- [Migration failure mid-swap] → Use transaction where supported; test migration against copy of prod schema in CI; keep migration idempotent steps clearly ordered
- [SQLite vs Postgres migration differences] → Run full test suite on SQLite; manual verify on Postgres staging if available
- [Orphan `expenses.user_id` null rows] → Pre-migration check; assign to sole Owner or abort with clear error
- [Pending verify-email links with old int id] → Accept breakage; single Owner can re-request verification
- [Third-party references to integer ids] → Grep and update tests, seeders, Livewire keys (cosmetic only)

## Migration Plan

1. Ship code + migration; run `php artisan migrate` on Laravel Cloud
2. Owner re-authenticates once; cache repopulates on first Today/Monthly read
3. Rollback: restore DB backup (PK type change is not safely reversible without backup)

## Open Questions

- None blocking — exact Laravel 12 `HasUuids` v7 hook (`newUniqueId()` override vs trait config) chosen at implement time per installed framework API.
