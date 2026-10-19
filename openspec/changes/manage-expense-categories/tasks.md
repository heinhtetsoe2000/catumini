## 1. Category Data Model

- [x] 1.1 Add the owner-scoped expense categories table and model with UUID identity, required name, and per-owner case-insensitive uniqueness; verify migration and model tests cover creation and duplicate names.
- [x] 1.2 Add a nullable category reference and relationship to expenses, with category deletion clearing references; verify existing expenses migrate as uncategorized and deleting a category preserves expense rows.
- [x] 1.3 Validate category ownership on expense create and update; verify attempts to assign another owner's category are rejected.

## 2. Category Management

- [x] 2.1 Add authenticated owner-scoped category listing, creation, and rename actions; verify feature tests cover valid changes, required names, and duplicate-name rejection.
- [x] 2.2 Add category deletion with confirmation and unassignment behavior; verify deleting a used category leaves its expenses intact and uncategorized.
- [x] 2.3 Add a category management screen and navigation entry consistent with the existing application; verify the screen is reachable when authenticated and does not expose other owners' categories.

## 3. Expense Assignment and Display

- [x] 3.1 Add optional owner-scoped category selection to expense creation and editing; verify creating, changing, clearing, and omitting a category through feature tests.
- [x] 3.2 Show assigned category names in Today and Monthly expense rows while keeping uncategorized entries visible; verify both views render category and uncategorized expenses.
- [x] 3.3 Confirm expense totals still include categorized and uncategorized expenses; verify Today and Monthly totals against the stored expense amounts.

## 4. Verification

- [x] 4.1 Run the focused category and expense-ledger Pest tests and verify all pass.
- [x] 4.2 Run Pint on dirty PHP files and verify no formatting changes remain.
- [x] 4.3 Run `openspec validate manage-expense-categories --strict` and verify the change artifacts and spec delta validate.