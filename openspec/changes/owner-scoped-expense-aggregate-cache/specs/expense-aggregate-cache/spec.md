## ADDED Requirements

### Requirement: Day total is cached per Owner and Spend date
The system SHALL expose a day total (sum of the Owner's Expense amounts for a given Spend date in Asia/Yangon) via a shared aggregate cache. The day total SHALL be filled lazy on first read and MUST NOT require caching the Expense row list.

#### Scenario: Cold read stores day total
- **WHEN** the Owner's day total for a Spend date is requested and that day key is absent
- **THEN** the system computes the sum from the database, stores it under an Owner-scoped day key using the Yangon `Y-m-d` calendar string, and returns that integer total

#### Scenario: Warm read returns cached day total
- **WHEN** the Owner's day total for a Spend date is requested and that day key is present
- **THEN** the system returns the cached integer without recomputing from Expense rows

### Requirement: Month rollup is cached as per-day sums
The system SHALL expose a month rollup for an Owner as a map of Spend date (`Y-m-d` in Asia/Yangon) to that day's sum. Empty days MUST be omitted. Month total, average of day sums, and day−average difference MUST be derived in application code from this map and/or day totals — not stored as separate authoritative cache values.

#### Scenario: Cold read stores month per-day sums
- **WHEN** the Owner's month rollup for a calendar month is requested and that month key is absent
- **THEN** the system groups that Owner's Expenses in the month by Spend date, stores the per-day sums under an Owner-scoped month key using Yangon `Y-m`, and returns the map

#### Scenario: Monthly view uses month rollup
- **WHEN** the Owner views Monthly for the current calendar month
- **THEN** day group totals shown match the cached (or freshly computed) per-day sums for that Owner and month

### Requirement: Expense lists are not cached
The system MUST NOT cache the collection of Expense rows for Today or day navigation. Lists SHALL continue to be loaded from the database.

#### Scenario: Today list remains a database read
- **WHEN** the Owner opens Today or navigates to another Spend date's list
- **THEN** the Expense rows are loaded from the database even if day or month aggregate keys are present

### Requirement: Immediate invalidation on Expense writes
The system SHALL invalidate the affected Owner day and month aggregate keys in the same request as an Expense create, update, or delete. A Spend date change SHALL invalidate both the previous and new day keys, and both month keys when the previous and new Spend dates fall in different calendar months.

#### Scenario: Create expense busts day and month keys
- **WHEN** the Owner creates an Expense with Spend date D in month M
- **THEN** the day key for D and the month key for M for that Owner are invalidated before the response completes

#### Scenario: Cross-month spend date edit busts both days and both months
- **WHEN** the Owner updates an Expense Spend date from D1 in month M1 to D2 in month M2 (M1 ≠ M2)
- **THEN** day keys for D1 and D2 and month keys for M1 and M2 for that Owner are invalidated before the response completes

#### Scenario: Totals stay correct after write
- **WHEN** the Owner creates, updates, or deletes an Expense and then reads Today aggregates or Monthly
- **THEN** displayed day total and month-derived figures match a fresh database computation for that Owner

### Requirement: Aggregate keys are Owner-scoped with Yangon dates
Cache keys for day totals and month rollups SHALL include the Owner id and Asia/Yangon calendar date strings (`Y-m-d` for days, `Y-m` for months). Keys MUST NOT use UTC-only dating or relative labels such as "today" or "current" as the sole date identity.

#### Scenario: Different Owners do not share aggregate keys
- **WHEN** two Owners each have Expenses on the same Spend date
- **THEN** each Owner's day total cache entry is distinct and does not include the other Owner's amounts

### Requirement: Long TTL safety net on aggregate keys
Cached day totals and month rollups SHALL carry a long TTL (on the order of one day) in addition to write-path invalidation. TTL expiry MUST NOT be the primary freshness mechanism after Expense writes.

#### Scenario: Entry expires after TTL if never invalidated
- **WHEN** an aggregate cache entry is written and no matching invalidation occurs
- **THEN** the entry becomes eligible for eviction after its configured long TTL
