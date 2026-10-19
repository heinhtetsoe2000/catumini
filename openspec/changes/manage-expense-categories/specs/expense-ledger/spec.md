## MODIFIED Requirements

### Requirement: Owner can create an expense with spend date
The system SHALL allow an authenticated user to create an expense with name, integer amount, optional description, an optional category they own, and a spend date that defaults to today in the application timezone (`Asia/Yangon`).

#### Scenario: Create expense defaults spend date to today
- **WHEN** the authenticated user submits a valid new expense without overriding the spend date
- **THEN** the expense is stored with `spent_on` equal to today's date in `Asia/Yangon` and associated to that user

#### Scenario: Create expense with backdated spend date
- **WHEN** the user creates a valid expense with a past spend date
- **THEN** the expense is stored with that spend date

#### Scenario: Create expense with a category
- **WHEN** the user creates a valid expense with a category they own
- **THEN** the expense is associated with that category

#### Scenario: Create expense without a category
- **WHEN** the user creates a valid expense without selecting a category
- **THEN** the expense is stored without a category

### Requirement: Owner can edit an expense
The system SHALL allow an authenticated user to update the name, amount, description, spend date, and optional category of an expense they own.

#### Scenario: Edit own expense
- **WHEN** the owner submits valid changes to one of their expenses
- **THEN** the stored expense reflects the new values

#### Scenario: Cannot edit another user's expense
- **WHEN** a user attempts to update an expense owned by a different user
- **THEN** the system denies the update

#### Scenario: Change or clear an expense category
- **WHEN** the owner updates an expense with a category they own or clears its category
- **THEN** the expense reflects the selected category or has no category

## ADDED Requirements

### Requirement: Owner can manage expense categories
The system SHALL allow an authenticated user to create, rename, and delete their own expense categories. Category names SHALL be required and unique within that user's categories, without regard to letter case.

#### Scenario: Create category
- **WHEN** the user submits a non-empty category name that is not already used by them
- **THEN** the category is created and associated with that user

#### Scenario: Rename own category
- **WHEN** the user submits a valid new name for a category they own
- **THEN** the category name is updated

#### Scenario: Reject duplicate category name
- **WHEN** the user creates or renames a category to a name already used by another of their categories, ignoring letter case
- **THEN** the change is rejected and the existing category remains unchanged

#### Scenario: Cannot manage another user's category
- **WHEN** a user attempts to view, rename, or delete a category owned by a different user
- **THEN** the system denies the operation and does not expose that category's data

### Requirement: Owner can assign an optional category to an expense
The system SHALL allow an expense to be associated with zero or one category owned by the expense's owner. Assigned category names SHALL be shown with the expense in Today and Monthly views.

#### Scenario: Display assigned category
- **WHEN** an expense with an assigned category appears in Today or Monthly
- **THEN** the expense displays its category name

#### Scenario: Reject another owner's category
- **WHEN** a user creates or edits an expense using a category owned by another user
- **THEN** the system rejects the category assignment

#### Scenario: Uncategorized expense remains valid
- **WHEN** an expense has no category
- **THEN** it remains visible and included in expense totals in Today and Monthly

### Requirement: Deleting a category preserves expenses
The system SHALL remove a deleted category's association from its expenses without deleting or changing those expenses' other data.

#### Scenario: Delete category assigned to expenses
- **WHEN** the owner deletes a category assigned to one or more expenses
- **THEN** those expenses remain stored and become uncategorized