## Requirements

### Requirement: Web MVP does not render NativePHP EDGE chrome
On the expense MVP pages (Today, Add/Edit expense, Monthly, Profile), the system SHALL NOT render NativePHP EDGE components such as `native:top-bar` or `native:bottom-nav`.

#### Scenario: Today page has no EDGE components
- **WHEN** an authenticated user opens the Today (`home`) page in a browser
- **THEN** the response does not include `native:top-bar` or `native:bottom-nav` markup

### Requirement: Standard web navigation is available
The authenticated user SHALL be able to navigate between Today, Add expense, Monthly (dashboard), and Profile using the web navigation (Breeze or equivalent) without requiring a native shell.

#### Scenario: Navigate Today to Add expense
- **WHEN** an authenticated user is on Today and follows the Add expense navigation link
- **THEN** they reach the add-expense form

#### Scenario: Navigate to Monthly
- **WHEN** an authenticated user follows the Monthly / dashboard navigation link
- **THEN** they reach the Monthly expense view
