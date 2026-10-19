## Context

The `Expense` model uses UUIDs, belongs to a `User`, and supports optional descriptive data. Expense ownership is enforced through user-scoped queries. The Today and Monthly views read expenses and totals; this change adds category management without changing how dates or totals are calculated.

## Goals / Non-Goals

**Goals:**
- Keep category data private to its owner and prevent cross-owner assignments.
- Keep category assignment optional so existing expenses and current entry flows remain valid.
- Preserve expense records when a category is removed.

**Non-Goals:**
- Built-in or pre-seeded categories, category colors/icons, budgets, reports, or category-based totals.
- Assigning multiple categories to one expense.

## Decisions

### 1. Owner-owned category records

- **Choice:** Add an `ExpenseCategory` model with a UUID, `user_id`, and required name. Enforce uniqueness by owner and case-insensitive name; scope category management and selection to the authenticated owner.
- **Rationale:** Categories are personal labels, not a shared global taxonomy. Owner-scoped uniqueness avoids duplicate labels without preventing different owners from using the same name.
- **Alternatives:** Global categories (do not fit the personal ledger); free-text labels (make renaming and consistent selection difficult).

### 2. Optional single category relationship

- **Choice:** Add nullable `category_id` to expenses and expose a nullable category relationship. Validate that the selected category belongs to the expense owner on both create and update.
- **Rationale:** A single optional relation supports simple selection and preserves existing uncategorized entries. Application-level owner validation is necessary because a normal foreign key does not ensure both records share an owner.
- **Alternatives:** Required assignment (would make existing and future expense entry less flexible); many-to-many categories (unneeded complexity for the requested behavior).

### 3. Clear assignments when deleting categories

- **Choice:** Deleting a category sets its expenses' `category_id` to null and leaves every expense row intact.
- **Rationale:** This matches the ledger's hard-delete behavior for expenses while avoiding accidental loss of financial history when cleaning up labels.
- **Alternatives:** Block deletion while in use (surprising cleanup workflow); cascade-delete expenses (data loss).

### 4. Category selection and presentation

- **Choice:** Provide owner-scoped category management in an authenticated screen, expose the same owner's categories in expense create/edit forms, and display a category label on assigned expenses in Today and Monthly.
- **Rationale:** Management, assignment, and confirmation are all discoverable without adding category analytics or changing totals.
- **Alternatives:** Category management only in the expense form (poor for renaming/deleting); display category only on edit (assignment is not visible during normal ledger use).

### 5. Keep totals unchanged

- **Choice:** Category data is descriptive only; existing Today and Monthly totals continue to include all expenses, including uncategorized ones.
- **Rationale:** The request adds organization, not new financial aggregation semantics.
- **Alternatives:** Filter or group totals by category (scope expansion requiring separate UX and requirements).

## Risks / Trade-offs

- [Case-insensitive uniqueness differs across database collations] → Normalize names consistently for validation and persistence, and enforce the owner/name uniqueness with a database-compatible normalized value or equivalent constraint.
- [A category reference can be submitted directly rather than through the picker] → Validate ownership on every write; never trust the submitted category ID alone.
- [Deleting categories removes classification from expense history] → Confirm the deletion action and preserve expense data; removed classifications are intentionally not recoverable.

## Migration Plan

1. Add the owner-scoped category table and nullable expense category reference in the same deployment; existing expenses remain uncategorized.
2. Deploy the management and assignment UI with owner validation and category deletion behavior.
3. Verify existing expenses still display, assigned categories remain owner-scoped, and totals include categorized and uncategorized expenses.
4. Rollback by removing the nullable reference and category table after reverting code. Category records and assignments created after deployment will be lost; expense rows remain intact.