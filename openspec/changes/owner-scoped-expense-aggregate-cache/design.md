## Context

Personal Expense Ledger (Laravel 12) serves **Today** via Livewire (`pages::home`) and **Monthly** via `DashboardController`. Both compute day totals and month rollups with repeated Eloquent queries; nothing uses `Cache` yet. Laravel Cloud cache is available; ADR 0004 locks Owner-scoped aggregate caching (not Expense lists) with immediate invalidation and Yangon-dated keys. App timezone is already `Asia/Yangon`.

## Goals / Non-Goals

**Goals:**

- Shared aggregate reader for day total (int) and month map of `Y-m-d` → day sum
- Today and Monthly use that reader for aggregates; lists remain direct DB reads
- Immediate invalidation on Expense create/update/delete via model events
- Owner-scoped keys with Asia/Yangon `Y-m-d` / `Y-m`; long TTL safety net
- Tests proving correctness after writes (including cross-month `spent_on` moves)

**Non-Goals:**

- Caching Expense row collections
- Eager warming of many days/months
- Changing visible ledger UX or expense-ledger product rules
- Forcing Redis locally (default `CACHE_STORE` may stay database; Cloud uses Redis)
- NativePHP / multi-Owner product work beyond key namespacing by `user_id`

## Decisions

### 1. Shared service as sole aggregate reader

- **Choice:** Introduce something like `App\Services\ExpenseAggregateCache` (exact name flexible) with `dayTotal(int $ownerId, CarbonInterface $day): int`, `monthDayTotals(int $ownerId, CarbonInterface $month): array` (or Collection of date => int), plus `invalidateForExpense(Expense $expense)` / invalidate by owner + dates.
- **Rationale:** ADR requires one place for keys and bust rules; Livewire and Dashboard must not duplicate key strings.
- **Alternatives:** Inline `Cache::remember` in each UI (rejected); hide cache inside Eloquent scopes (surprising).

### 2. Key shape and TTL

- **Choice:** Keys `owner:{id}:day:{Y-m-d}` and `owner:{id}:month:{Y-m}` using dates formatted in `Asia/Yangon`. TTL ≈ 24 hours (or until end of calendar day — pick one constant in implementation).
- **Rationale:** Matches ADR; avoids UTC midnight skew; TTL only backs up missed invalidation.
- **Alternatives:** “current” relative keys (rejected); no TTL (rejected).

### 3. Month payload and derived metrics

- **Choice:** Cache only non-empty per-day sums. Derive month total, average (mean of day sums), and Today’s difference (`dayTotal - average`) in PHP.
- **Rationale:** Single source of truth for day groups and average; no divergent cached average key.
- **Alternatives:** Cache average separately (drift risk); cache full rows (out of scope).

### 4. Lazy `Cache::remember` on read

- **Choice:** First read for a day/month loads from DB and stores; subsequent hits use cache until invalidation or TTL.
- **Rationale:** ADR lazy-on-read; fits low traffic and historical day navigation.
- **Alternatives:** Eager warm (rejected).

### 5. Invalidation via Expense model events

- **Choice:** Observer or `booted()` listeners on `created` / `updated` / `deleted`. On update, if `spent_on` changed, invalidate old and new day keys and both month keys when months differ; always invalidate at least the current spend day + its month.
- **Rationale:** Covers Livewire today and future writers; matches ADR.
- **Alternatives:** Explicit bust only in Livewire `save` (easy to miss).

### 6. Wire UI readers only for aggregates

- **Choice:** Replace `calculateTotal` / `calculateAverage` (and Dashboard monthly grouping totals) with service calls. Keep `Expense::…->get()` for the Today list.
- **Rationale:** Aggregates-only decision; list invalidation cost not worth it for MVP volume.

### 7. Testing approach

- **Choice:** Feature/unit tests with `Cache` array driver or `Cache::spy`/`forget` assertions; assert day total and month map refresh after create/update/delete and after cross-month `spent_on` change; assert UI totals still match DB.
- **Rationale:** Spec scenarios map directly to Pest cases.

## Risks / Trade-offs

- [Observer fires during seeders/factories] → Accept busts (cheap) or document that tests use `Cache::flush` in setup
- [Day total key vs month map can disagree briefly if only one invalidated] → Always invalidate both day and month for affected dates on every write
- [Legacy `HomeController` still has uncached aggregate code but is unrouted] → Leave alone or thin-wrap later; do not block this change
- [Long TTL with forgotten invalidation path] → Centralize bust in observer; tests cover mutations

## Migration Plan

1. Deploy code with service + observer + UI wiring (no schema migration)
2. Ensure Laravel Cloud `CACHE_STORE` points at Redis/cache
3. Rollback: revert deploy; cache entries expire via TTL; no data loss

## Open Questions

- None blocking — TTL exact seconds (e.g. `86400`) can be chosen at implement time and kept as a named constant.
