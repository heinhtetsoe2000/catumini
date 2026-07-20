## Why

Laravel Cloud cache is provisioned, but the ledger still recomputes Today day totals and Monthly rollups from the database on every read. We want Owner-scoped aggregate caching now—without caching Expense lists—so MVP latency improves and key/invalidation shape will not block a later public multi-user launch. Decisions are locked in `docs/adr/0004-owner-scoped-expense-aggregate-cache.md`.

## What Changes

- Introduce a shared Expense aggregate cache reader for day totals and month per-day sum maps
- Wire **Today** (Livewire) and **Monthly** (`DashboardController`) to that reader for aggregates only; Expense lists stay uncached DB reads
- Invalidate day and month keys immediately on Expense create/update/delete (model events), including both old and new **Spend date**/month when `spent_on` changes
- Use Asia/Yangon calendar strings in cache keys; long TTL as a safety net alongside write invalidation
- Add Pest coverage for cache hit/miss, invalidation (including cross-month date moves), and UI totals remaining correct after writes

## Capabilities

### New Capabilities

- `expense-aggregate-cache`: Owner-scoped day total and month per-day-sum caching with lazy fill, immediate write invalidation, and derived month total/average/difference

### Modified Capabilities

- (none — visible Today/Monthly behavior stays the same; caching is an internal read-path change covered by the new capability)

## Impact

- New PHP service (or equivalent) for aggregate read + key helpers; Expense model observer/events for invalidation
- `resources/views/pages/⚡home.blade.php` and `DashboardController` switch aggregate queries to the shared reader
- Cache store: app continues to use configured `CACHE_STORE` (Redis on Laravel Cloud; local may remain `database`/`array` in tests)
- Docs: ADR 0004 already accepted; no glossary changes beyond the existing CONTEXT pointer
- Tests: feature/unit coverage for aggregates + invalidation; use `Cache::fake()` or array store as appropriate
- Out of scope: caching Expense row lists, eager warming, NativePHP-specific cache, changing `CACHE_STORE` defaults in `.env.example` unless needed for Cloud docs
