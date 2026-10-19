## Why

Expense categories currently identify spending only by name, which makes them harder to scan in expense lists. Letting each owner choose a category color and icon adds a quick visual cue while keeping the existing category workflow intact.

## What Changes

- Allow owners to set or change an optional color and icon for each expense category.
- Limit selectable colors to a curated soft, muted palette.
- Render a category's chosen color as the category background and its chosen icon beside the category name in expense items.
- Limit icon choices to the icons supported by Flux Free.
- Preserve categories without a selected color or icon and existing expense behavior.

## Capabilities

### New Capabilities

None.

### Modified Capabilities

- `expense-ledger`: Add category appearance options and display their color and icon with categorized expenses.

## Impact

- Expense category persistence, validation, and category management UI.
- Expense item presentation in Today and Monthly views.
- Pest coverage for category appearance updates and rendering.