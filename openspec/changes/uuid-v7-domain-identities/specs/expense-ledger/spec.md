## ADDED Requirements

### Requirement: Every Expense belongs to an Owner
The system SHALL require every Expense to reference an Owner via `user_id`. Orphan Expense rows (null or invalid `user_id`) MUST NOT exist after migration.

#### Scenario: Created expense is linked to authenticated Owner
- **WHEN** an authenticated Owner creates an Expense
- **THEN** the stored Expense has a non-null `user_id` equal to that Owner's id

#### Scenario: Database enforces Owner reference
- **WHEN** an attempt is made to insert or retain an Expense with a null or unknown `user_id`
- **THEN** the database rejects the row via NOT NULL and foreign key constraints

### Requirement: Expense identity is assigned at creation
The system SHALL assign an **Expense identity** (UUID v7 primary key) when the Expense is first recorded. That identity MUST NOT change for the life of the row.

#### Scenario: Expense retains id after edit
- **WHEN** the Owner updates an existing Expense
- **THEN** the Expense primary key is identical to the value assigned at creation
