## 1. Schema and model

- [x] 1.1 Migration: `incomes` table (uuid PK, `user_id`, `name`, `amount` int, `description` nullable, `received_on` date, timestamps)
- [x] 1.2 Migration: `users.income_tracking` boolean default `false`
- [x] 1.3 Create `Income` model (factory, casts, `user()` relationship, `currentUser()` / date scopes mirroring Expense patterns, `isOwnedBy()`)
- [x] 1.4 Register `IncomeObserver` (or booted events) for aggregate cache invalidation

## 2. Available aggregate cache (ADR 0008)

- [x] 2.1 Extend ledger aggregate reader with `totalReceived`, `totalSpent`, `available(userId)` applying 0-floor rule
- [x] 2.2 Add `monthIncomeTotal(userId, month)` for current-month received on Income tab / History strip
- [x] 2.3 Invalidate global and month income keys on Income create/update/delete (including `received_on` moves)
- [x] 2.4 Ensure Expense writes also bust global totals used for **Available**
- [x] 2.5 Pest coverage for cache hit/miss and invalidation across Income and Expense writes

## 3. Income tracking preference

- [x] 3.1 Add Profile **Income tracking** instant toggle (Livewire or PATCH endpoint)
- [x] 3.2 Persist on `users.income_tracking`; redirect or refresh nav after toggle
- [x] 3.3 Pest: default off; toggle on/off; income data survives toggle off

## 4. Income tab (Livewire)

- [x] 4.1 Route + `pages::income` Livewire page (guarded: redirect or 404 when tracking off)
- [x] 4.2 Month row: current-month received, spent, **month net**
- [x] 4.3 **Available** row (global, with 0-floor and negative alarm styling)
- [x] 4.4 All-time income list (newest first) + add-income modal/form
- [x] 4.5 Nested `income.edit` component for edit/delete (parity with expense edit)
- [x] 4.6 Pest: CRUD, ownership denial, received_on backdating

## 5. Shell and balance strips

- [x] 5.1 Conditional **Income** nav item (mobile icon-only, desktop text) between Home and History per ADR 0007
- [x] 5.2 Home: compact strip (today's spent + **Available**) when tracking on
- [x] 5.3 History: compact strip (current-month received/spent + **Available**) when tracking on
- [x] 5.4 Pest: nav visible/hidden; strips absent when tracking off

## 6. Finish

- [x] 6.1 Run Pint on dirty PHP files
- [x] 6.2 Run `php artisan test --compact` for affected tests
- [x] 6.3 Archive or merge spec deltas into `openspec/specs/` when change completes
