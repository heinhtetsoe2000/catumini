## Requirements

### Requirement: Owner can create an expense with spend date
The system SHALL allow an authenticated user to create an expense with name, integer amount, optional description, and a spend date that defaults to today in the application timezone (`Asia/Yangon`).

#### Scenario: Create expense defaults spend date to today
- **WHEN** the authenticated user submits a valid new expense without overriding the spend date
- **THEN** the expense is stored with `spent_on` equal to today's date in `Asia/Yangon` and associated to that user

#### Scenario: Create expense with backdated spend date
- **WHEN** the authenticated user submits a valid expense with a past spend date
- **THEN** the expense is stored with that spend date

### Requirement: Owner can edit an expense
The system SHALL allow an authenticated user to update name, amount, description, and spend date of an expense they own.

#### Scenario: Edit own expense
- **WHEN** the owner submits valid changes to one of their expenses
- **THEN** the stored expense reflects the new values

#### Scenario: Cannot edit another user's expense
- **WHEN** a user attempts to update an expense owned by a different user
- **THEN** the system denies the update

### Requirement: Owner can hard-delete an expense
The system SHALL allow an authenticated user to permanently delete an expense they own. Soft deletes are not used.

#### Scenario: Delete own expense
- **WHEN** the owner confirms deletion of one of their expenses
- **THEN** the expense row is removed from the database and no longer appears in Today or Monthly

#### Scenario: Cannot delete another user's expense
- **WHEN** a user attempts to delete an expense owned by a different user
- **THEN** the system denies the deletion

### Requirement: Amounts are integers labeled Ks
The system SHALL store expense amounts as integers and display them with the currency label `Ks` in user-facing lists and totals.

#### Scenario: Display amount with Ks
- **WHEN** an expense with amount `3500` is shown in a list or total
- **THEN** the UI includes both the formatted integer and the label `Ks`

### Requirement: Today view uses spend date
The system SHALL list and total the authenticated user's expenses whose spend date is today in `Asia/Yangon`.

#### Scenario: Today includes backdated entry for today
- **WHEN** the user creates an expense with spend date equal to today and views Today
- **THEN** that expense appears in today's list and contributes to today's total

#### Scenario: Today excludes other spend dates
- **WHEN** the user has expenses with spend dates other than today
- **THEN** those expenses do not appear in the Today list or total

### Requirement: Monthly view uses spend date
The system SHALL group and total the authenticated user's expenses for the current calendar month in `Asia/Yangon` by spend date (day).

#### Scenario: Monthly groups by spend day
- **WHEN** the user views Monthly with multiple expenses in the current month
- **THEN** expenses are grouped by their `spent_on` day and totals reflect those groups

#### Scenario: Empty month is safe
- **WHEN** the authenticated user has no expenses in the current month
- **THEN** the Monthly view loads without error and shows zero totals (no divide-by-zero)

### Requirement: Expenses only
The system SHALL track money-out expenses only; income and transfers are out of scope for this capability.

#### Scenario: No income entry path
- **WHEN** a user uses the MVP expense flows
- **THEN** there is no UI or API for recording income
