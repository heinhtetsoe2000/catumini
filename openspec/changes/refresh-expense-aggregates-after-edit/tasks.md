## 1. Reproduce and Fix Invalidation

- [x] 1.1 Trace the user-facing Expense amount edit path and add warmed-cache regression tests for the missing page refresh notification
- [x] 1.2 Notify parent pages after Expense edits and refresh their aggregate state; verify the focused aggregate-cache tests pass

## 2. Verify User-Facing Aggregates

- [x] 2.1 Add coverage that edits an Expense amount and confirms Today and Monthly totals show the new amount; verify with the affected Pest feature tests
- [x] 2.2 Add coverage that edits an Expense amount with Income tracking enabled and confirms total spent and **Available** refresh; verify with the affected Pest feature tests
- [x] 2.3 Run `vendor/bin/pint --dirty --format agent` and `php artisan test --compact` for the affected tests; confirm formatting and all targeted tests pass