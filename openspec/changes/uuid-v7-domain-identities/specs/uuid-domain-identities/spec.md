## ADDED Requirements

### Requirement: Owner and Expense use UUID v7 primary keys
The system SHALL store **Owner identity** (`users.id`) and **Expense identity** (`expenses.id`) as UUID version 7 strings. Primary keys MUST NOT be auto-incrementing integers.

#### Scenario: New Owner receives UUID v7 at provision
- **WHEN** a User row is created via seed, Artisan provision, or factory
- **THEN** the row's `id` is a valid UUID v7 string assigned before insert

#### Scenario: New Expense receives UUID v7 at creation
- **WHEN** an Expense row is created on the web path
- **THEN** the row's `id` is a valid UUID v7 string assigned before insert

### Requirement: Expense identity is client-authoritative
The **Expense identity** assigned at first capture MUST remain the canonical primary key after sync. The system MUST NOT reassign Expense primary keys on write.

#### Scenario: Expense id is stable after update
- **WHEN** an Owner edits an Expense's name, amount, description, or spend date
- **THEN** the Expense `id` value is unchanged

### Requirement: In-place migration preserves domain rows
The system SHALL provide a migration that converts existing integer `users.id` and `expenses.id` values to UUID v7 without dropping **Owner** or **Expense** business data.

#### Scenario: Existing expenses survive migration
- **WHEN** the migration runs against a database with existing User and Expense rows
- **THEN** every Expense row remains with the same business fields (name, amount, description, spent_on) and a new UUID v7 primary key

#### Scenario: Expense remains linked to same Owner after migration
- **WHEN** the migration completes for an Expense that belonged to an Owner before migration
- **THEN** the Expense `user_id` references that same Owner's new UUID primary key

### Requirement: Deploy cutover clears sessions and cache
The migration SHALL truncate active login sessions and flush the application cache store so auth and aggregate keys do not reference obsolete integer Owner ids.

#### Scenario: Sessions cleared on migrate
- **WHEN** the UUID migration completes
- **THEN** no session rows remain that reference the pre-migration integer user ids

#### Scenario: Cache flushed on migrate
- **WHEN** the UUID migration completes
- **THEN** the application cache store is empty so aggregate keys rebuild with UUID Owner ids

### Requirement: Framework tables keep integer ids
The system MUST NOT change primary key types on Laravel framework tables such as `jobs`, `failed_jobs`, or `cache`.

#### Scenario: Jobs table unchanged
- **WHEN** the UUID migration runs
- **THEN** the `jobs` table primary key remains an auto-increment integer
