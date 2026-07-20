## 1. Aggregate cache service

- [x] 1.1 Create `ExpenseAggregateCache` (or equivalent) with Owner-scoped day/month key helpers using Asia/Yangon `Y-m-d` / `Y-m`
- [x] 1.2 Implement lazy `dayTotal(ownerId, day)` via `Cache::remember` (integer sum, long TTL constant)
- [x] 1.3 Implement lazy `monthDayTotals(ownerId, month)` returning per-day sums map (omit empty days, long TTL)
- [x] 1.4 Implement invalidation helpers: forget day key(s) and month key(s); support previous+new Spend dates on update

## 2. Write-path invalidation

- [x] 2.1 Register Expense model observer or `booted` events for created / updated / deleted
- [x] 2.2 On create/delete: invalidate Spend date day + its month for that Owner
- [x] 2.3 On update: invalidate old and new day keys; invalidate both months when `spent_on` crosses a month boundary

## 3. Wire Today and Monthly readers

- [x] 3.1 Update Livewire Today (`pages::home`) to use the service for day total, monthly average (from month map), and difference; keep Expense list as DB `get()`
- [x] 3.2 Update `DashboardController` Monthly to build day groups / total / average from `monthDayTotals`
- [x] 3.3 Run Pint on dirty PHP files

## 4. Tests

- [x] 4.1 Add Pest coverage for cold/warm day total and month rollup (cache store/hit behavior)
- [x] 4.2 Assert create/update/delete invalidates the correct day and month keys
- [x] 4.3 Assert cross-month `spent_on` edit invalidates both days and both months
- [x] 4.4 Assert Today/Monthly displayed aggregates match DB after writes; Owners do not share keys
- [x] 4.5 Run `php artisan test --compact` for the new/affected tests
