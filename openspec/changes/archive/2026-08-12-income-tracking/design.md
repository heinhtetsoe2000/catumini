## Context

Personal Expense Ledger (Laravel 12) serves **Home** (Today expenses) via Livewire, **History** (Monthly) via `DashboardController`, and optional preferences on Profile. Income is explicitly out of scope in the current `expense-ledger` spec. ADR 0004 caches Expense aggregates; ADR 0007 adds a conditional **Income** nav peer; ADR 0008 caches **Available** and month income totals. App timezone is `Asia/Yangon`; identities use UUID v7 (ADR 0005).

## Goals / Non-Goals

**Goals:**

- `users.income_tracking` boolean (default `false`), instant toggle on Profile
- `Income` model mirroring `Expense` fields with `received_on` instead of `spent_on`
- **Available** = total received − total spent; display `0` until first Income row exists; then honest value including negative with alarm styling
- **Month net** on Income tab = current-month received − current-month spent (Yangon calendar)
- Income tab: month row + **Available** row + all-time income list (newest first) + add/edit/delete
- Home strip: today's spent + **Available**; History strip: current-month received/spent + **Available**
- Nav: **Income** between Home and History when tracking on; hidden when off
- Owner-scoped aggregate cache for totals; invalidate on Income and Expense writes
- Pest tests for toggle, CRUD, authorization, **Available** rules, nav visibility

**Non-Goals:**

- Changing expense-only experience when tracking is off
- Monthly reset of **Available**
- Blocking expense entry when **Available** is negative
- Income categories, recurring rules, bank sync
- Caching Income row lists

## Decisions

### 1. Parallel `Income` model (not unified Transaction)

- **Choice:** Separate `incomes` table and `Income` model; same ownership and CRUD patterns as `Expense`.
- **Rationale:** Glossary avoids **Transaction**; minimal surprise vs existing Expense code paths.
- **Alternatives:** Unified ledger entry with direction enum (rejected — vocabulary and migration cost).

### 2. `income_tracking` on User, instant toggle

- **Choice:** Boolean column; Livewire or dedicated Profile control applies immediately (like **Appearance**), no Save batching.
- **Rationale:** Confirmed in grilling; off = hide UI, keep rows.
- **Alternatives:** Save-with-profile form (rejected).

### 3. **Available** display rules in application layer

- **Choice:** If `Income::where(user_id)->count() === 0`, return display **Available** `0`. Else `sum(incomes.amount) - sum(expenses.amount)`.
- **Rationale:** Q22-C / Q27-A; strips and Income tab share one helper.
- **Alternatives:** Show negative from expenses-only history (rejected).

### 4. Aggregate cache extension (ADR 0008)

- **Choice:** Shared reader (extend `ExpenseAggregateCache` or sibling `LedgerAggregateCache`) with Owner keys for total received, total spent, month income total; lazy fill + observer invalidation on both models.
- **Rationale:** Same pattern as ADR 0004; Income tab and strips must not scan all rows each render.
- **Alternatives:** Compute on read only (rejected for scale); store **Available** as DB column (rejected — derived truth).

### 5. Income UI via Livewire page

- **Choice:** `Route::livewire('/income', 'pages::income')` (or equivalent) with nested edit modal matching expense edit pattern.
- **Rationale:** Matches Today CRUD convention.
- **Alternatives:** Controller + Blade only (rejected for interactive CRUD).

### 6. Nav wiring (ADR 0007)

- **Choice:** Conditional `@if(auth()->user()->income_tracking)` around Income items in `navigation.blade.php` and `bottom-navigation.blade.php`; mobile icon-only + `aria-label="{{ __('Income') }}"`; desktop text **Income**.
- **Rationale:** Expense-only shell unchanged when off.
- **Alternatives:** Always show tab disabled (rejected).

### 7. Negative **Available** styling

- **Choice:** Distinct alarm color/badge when **Available** < 0 after first Income exists; no modal warnings on expense save.
- **Rationale:** Q10-B.

### 8. Authorization

- **Choice:** Inline `isOwnedBy($user)` + `abort_unless(..., 403)` on Income mutations — same as Expense today.
- **Rationale:** Existing convention; no new policies in MVP.

## Risks / Trade-offs

- [ADR 0003 vs four-item mobile bar] → Document supersession in ADR 0007; accept denser bottom nav when tracking on
- [Two aggregate services vs one] → Prefer single ledger aggregate entry point to avoid double invalidation misses
- [Toggle off mid-session] → Livewire/nav must re-render; full page redirect after toggle is acceptable
- [Existing expense-only tests] → Must pass unchanged with default `income_tracking = false`

## Migration Plan

1. Migration: `incomes` table + `users.income_tracking`
2. Deploy models, cache, observers, UI behind preference default false
3. Rollback: drop column/table; remove nav items; expense ledger unaffected

## Open Questions

- Income nav icon choice (e.g. `banknotes`, `arrow-down-circle`) — pick at implement time; must not be house/chart per glossary.
