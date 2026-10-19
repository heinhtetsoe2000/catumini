## Why

Expenses cannot currently be grouped by a user-defined purpose, and the original ledger deliberately deferred categories. Adding owner-managed categories now lets the Owner organize spending while keeping existing expense entry and history intact.

## What Changes

- Add owner-scoped expense category management to create, rename, and delete categories.
- Allow an expense to have no category or one category owned by the same user.
- Add category selection to expense creation and editing, and show assigned categories in expense views.
- Preserve existing expenses as uncategorized; deleting a category removes its assignment from expenses without deleting those expenses.

## Capabilities

### New Capabilities

- None

### Modified Capabilities

- `expense-ledger`: Extend expense create/edit and presentation behavior with optional owner-managed categories.

## Impact

- Data model and migration: add owner-scoped categories and an optional category reference on expenses.
- Expense create/edit validation and persistence, category management routes/actions, and Today/Monthly views.
- Pest coverage for category ownership, CRUD, assignment, and preservation of expenses when categories are deleted.