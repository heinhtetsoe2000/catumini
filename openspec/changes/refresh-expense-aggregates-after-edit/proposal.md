## Why

Editing an Expense amount can persist the new value while cached spend totals and **Available** continue showing the old amount. The existing aggregate-cache contract requires Expense writes to invalidate these values, so this change makes the amount-edit case explicit and adds regression coverage for warmed caches.

## What Changes

- Ensure an Expense amount edit refreshes the Owner's affected day and month spend aggregates, global total spent, and derived **Available** before the update response completes.
- Add regression scenarios that warm relevant caches, edit an Expense amount, then verify refreshed aggregate values in the affected views.
- Preserve Owner isolation and existing behavior for Expense lists and income tracking.

## Capabilities

### New Capabilities

None.

### Modified Capabilities

- `expense-ledger`: Require Today and Monthly spend totals to reflect an edited Expense amount on subsequent reads.
- `income-tracking`: Clarify that updating an Expense amount invalidates cached spend totals used by **Available** and that subsequent reads show the new amount.

## Impact

- Expense update/event invalidation path and `ExpenseAggregateCache` behavior.
- Pest coverage for expense amount updates with warmed daily, monthly, total-spent, and **Available** caches.
- No database schema, dependency, or public API changes expected.