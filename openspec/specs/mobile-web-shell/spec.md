## Requirements

### Requirement: Web MVP does not render NativePHP EDGE chrome
On the expense MVP pages (Today, Add/Edit expense, Monthly, Profile), the system SHALL NOT render NativePHP EDGE components such as `native:top-bar` or `native:bottom-nav`.

#### Scenario: Today page has no EDGE components
- **WHEN** an authenticated user opens the Today (`home`) page in a browser
- **THEN** the response does not include `native:top-bar` or `native:bottom-nav` markup

### Requirement: Standard web navigation is available
The authenticated user SHALL be able to navigate between Today, Monthly (dashboard), and Profile using the web navigation (Breeze or equivalent, styled with ledger ink) without requiring a native shell. Add expense SHALL remain available from the Today page CTA and MUST NOT appear as a primary or responsive top-navigation item.

#### Scenario: Navigate Today to Add expense via Today CTA
- **WHEN** an authenticated user is on Today and follows the Add Expense control on the Today page
- **THEN** they reach the add-expense form

#### Scenario: Navigate to Monthly
- **WHEN** an authenticated user follows the Monthly / dashboard navigation link
- **THEN** they reach the Monthly expense view

#### Scenario: Top navigation has no Add item
- **WHEN** an authenticated user opens Today
- **THEN** the primary navigation does not include an Add (or Add expense) nav item linking to the add-expense route as a top-level peer of Today and Monthly

### Requirement: Authenticated shell uses ledger ink branding
The authenticated mobile-web shell (Today, Add/Edit expense, Monthly, Profile) SHALL use the shared ledger ink theme and wordmark branding consistent with the Login gate and guest layout, while continuing to provide web navigation without NativePHP EDGE chrome.

#### Scenario: Today navigation remains usable under ledger ink
- **WHEN** an authenticated user opens the Today (`home`) page in a browser
- **THEN** they can navigate to Monthly and Profile via web navigation and to Add expense via the Today page CTA
- **AND** the response does not include `native:top-bar` or `native:bottom-nav` markup
- **AND** the navigation brand is a text wordmark of the configured app name, not the Laravel logo SVG
