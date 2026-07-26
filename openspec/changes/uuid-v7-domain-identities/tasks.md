## 1. In-place database migration

- [x] 1.1 Create migration to add temporary UUID columns on `users` and `expenses`
- [x] 1.2 Backfill `users` and `expenses` with UUID v7; build old integer → UUID map for users
- [x] 1.3 Backfill `expenses.user_id` from map; fail or fix any null orphans before NOT NULL
- [x] 1.4 Swap primary keys: drop integer ids, promote UUID columns to `id`, add FK + NOT NULL on `expenses.user_id`
- [x] 1.5 Truncate `sessions` and flush cache at end of migration

## 2. Model and ID generation

- [x] 2.1 Add `HasUuids` (v7) to `User` and `Expense`; ensure non-incrementing string keys
- [x] 2.2 Remove integer cast on `Expense.user_id`; fix `isOwnedBy()` string comparison
- [x] 2.3 Verify factories and seeders create valid UUID rows without manual integer ids

## 3. Application code updates

- [x] 3.1 Update `ExpenseAggregateCache` — `string $ownerId`, UUID key format (`owner:%s:…`)
- [x] 3.2 Remove `(int)` casts on `auth()->id()`, `$user->id`, and `$expense->user_id` in controllers, Livewire, and services
- [x] 3.3 Run Pint on dirty PHP files

## 4. Tests

- [x] 4.1 Add migration test: existing User + Expense rows survive with UUID ids and preserved FK link
- [x] 4.2 Assert new User/Expense models receive UUID v7 on create
- [x] 4.3 Update existing Expense ledger and aggregate cache tests for string owner ids
- [x] 4.4 Run `php artisan test --compact` for affected tests
