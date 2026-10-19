## ADDED Requirements

### Requirement: Owner can customize expense category appearance
The system SHALL allow an authenticated user to choose optional color and icon values when creating an expense category and to set, change, or clear those values on a category they own. Selectable colors SHALL be limited to a curated soft, muted palette and SHALL exclude saturated or dark color options. Icon choices SHALL be limited to the icons supported by Flux Free, and color and icon values not offered by the system SHALL be rejected.

#### Scenario: Create category with appearance
- **WHEN** the owner creates a category with a color and a supported icon
- **THEN** the category retains both selected appearance options

#### Scenario: Set category color and icon
- **WHEN** the owner updates a category with a color and a supported icon
- **THEN** the category retains both selected appearance options

#### Scenario: Color selector offers only soft colors
- **WHEN** the owner views the category color options
- **THEN** every offered color is soft and muted, with no saturated or dark options

#### Scenario: Change or clear category appearance
- **WHEN** the owner changes or clears a category color or icon
- **THEN** the category reflects the new selection or has no value for the cleared option

#### Scenario: Reject unsupported appearance values
- **WHEN** the owner submits a color or icon that is not offered by the system
- **THEN** the update is rejected and the category's existing appearance remains unchanged

#### Scenario: Cannot customize another owner's category
- **WHEN** a user attempts to change the appearance of a category owned by another user
- **THEN** the system denies the operation and does not change that category

### Requirement: Expense items display category appearance
When an expense item displays an assigned category, the system SHALL display the category's selected icon and use its selected color as the category background. If either option is unset, the item SHALL retain the existing presentation for that option.

#### Scenario: Display selected category appearance
- **WHEN** an expense with a category that has a color and icon appears in an expense list
- **THEN** the category is shown with its selected background color and icon

#### Scenario: Display category with no selected appearance
- **WHEN** an expense with a category that has no selected color or icon appears in an expense list
- **THEN** the category remains readable and uses the existing presentation for each unset option