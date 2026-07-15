## ADDED Requirements

### Requirement: Authenticated shell uses ledger ink branding
The authenticated mobile-web shell (Today, Add/Edit expense, Monthly, Profile) SHALL use the shared ledger ink theme and wordmark branding consistent with the Login gate and guest layout, while continuing to provide web navigation without NativePHP EDGE chrome.

#### Scenario: Today navigation remains usable under ledger ink
- **WHEN** an authenticated user opens the Today (`home`) page in a browser
- **THEN** they can navigate to Add expense, Monthly, and Profile via web navigation
- **AND** the response does not include `native:top-bar` or `native:bottom-nav` markup
- **AND** the navigation brand is a text wordmark of the configured app name, not the Laravel logo SVG

## MODIFIED Requirements

### Requirement: Standard web navigation is available
The authenticated user SHALL be able to navigate between Today, Add expense, Monthly (dashboard), and Profile using the web navigation (Breeze or equivalent, styled with ledger ink) without requiring a native shell.

#### Scenario: Navigate Today to Add expense
- **WHEN** an authenticated user is on Today and follows the Add expense navigation link
- **THEN** they reach the add-expense form

#### Scenario: Navigate to Monthly
- **WHEN** an authenticated user follows the Monthly / dashboard navigation link
- **THEN** they reach the Monthly expense view
