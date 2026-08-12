## Why

The ledger tracks money-out well, but Owners who enable **Income tracking** need to know how much they received, when, and **Available** to spend — with spending on **Home** and **History** shown in that context. Decisions are locked in `CONTEXT.md`, ADR 0007 (conditional **Income** nav), and ADR 0008 (**Available** aggregate cache).

## What Changes

- Add **Income tracking** Owner preference (Profile toggle, default off, instant apply; data kept when off)
- Add **Income** entity with full CRUD parity to **Expense** (name, integer **Ks**, optional description, **Received date**, UUID v7, owner scoping)
- Add **Income** nav destination (between **Home** and **History**) when tracking is on; Income tab with month row, **Available** row, all-time list, add-income
- Add **Available** strips on **Home** (today's spent + global **Available**) and **History** (current-month in/out + global **Available**) when tracking is on
- Extend aggregate caching for global received/spent/**Available** and month income totals; invalidate on Income and Expense writes
- Remove expense-ledger “expenses only / no income path” requirement (superseded by optional capability)
- Add Pest coverage for toggle, CRUD, **Available** math (0-floor, negative alarm), nav visibility, and cache invalidation

## Capabilities

### New Capabilities

- `income-tracking`: Optional Owner preference, Income CRUD, **Available** / **month net**, Income tab, balance strips, aggregate cache

### Modified Capabilities

- `mobile-web-shell`: Conditional **Income** primary destination when **Income tracking** is on (ADR 0007)
- `expense-ledger`: Drop “expenses only” requirement; expense behavior unchanged when tracking is off
- `profile-logout`: Profile exposes **Income tracking** instant toggle

## Impact

- Database: `incomes` table; `users.income_tracking` (boolean, default false)
- Models: `Income`, `User` cast/fillable; `IncomeObserver` for cache invalidation
- Services: extend or add ledger aggregate reader for **Available** (ADR 0008)
- UI: new Livewire Income page, nav partials, Profile toggle, Home/History strips
- Tests: feature tests for income flows, toggle, aggregates, authorization
- Docs: `CONTEXT.md`, ADR 0007, ADR 0008
- Out of scope: bank linking, categories, recurring income, transfers, Buddhist calendar, carrying **Available** as a stored balance field (always derived)
