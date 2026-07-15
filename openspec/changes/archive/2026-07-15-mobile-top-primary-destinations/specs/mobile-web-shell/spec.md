## MODIFIED Requirements

### Requirement: Standard web navigation is available
The authenticated user SHALL be able to navigate between Today, Monthly (dashboard), and Profile using the web navigation shell (Flux Free components inside the top-nav information architecture, styled with ledger ink) without requiring a native shell. On viewports below the `sm` breakpoint, Today and Monthly SHALL be always-visible primary destinations in the top bar (one tap, not behind a menu); they MAY be presented as icon-only controls whose accessible names are “Today” and “Monthly” and whose icons MUST use a calendar-day / calendar-month metaphor (MUST NOT use house or chart icons). On `sm` and larger viewports, Today and Monthly SHALL remain text-labeled top-nav peers. Profile SHALL remain reachable from a compact account menu control (MUST NOT require a hamburger drawer for Today/Monthly or for Profile/Log Out). Add expense SHALL remain available from the Today page CTA and MUST NOT appear as a primary or responsive top-navigation item. The shell MUST NOT introduce a sidebar or bottom navigation pattern. Current/selected state for Today and Monthly SHALL apply only when the current route is exactly `home` or `dashboard` respectively (Add/Edit expense and Profile SHALL leave both peers unselected). The application wordmark SHALL continue to link to Today (`home`).

#### Scenario: Navigate Today to Add expense via Today CTA
- **WHEN** an authenticated user is on Today and follows the Add Expense control on the Today page
- **THEN** they reach the add-expense form

#### Scenario: Navigate to Monthly
- **WHEN** an authenticated user follows the Monthly / dashboard navigation control
- **THEN** they reach the Monthly expense view

#### Scenario: Top navigation has no Add item
- **WHEN** an authenticated user opens Today
- **THEN** the primary navigation does not include an Add (or Add expense) nav item linking to the add-expense route as a top-level peer of Today and Monthly

#### Scenario: Mobile Today and Monthly are one-tap peers
- **WHEN** an authenticated user opens Today on a small viewport (below `sm`)
- **THEN** Today and Monthly primary destination controls are visible in the top bar without opening a menu
- **AND** no hamburger / bars toggle is required to reach Monthly
- **AND** the response does not include bottom-navigation markup (including `native:bottom-nav`)

#### Scenario: Mobile account menu without hamburger drawer
- **WHEN** an authenticated user opens the account control on a small viewport
- **THEN** they can reach Profile and Log Out
- **AND** Today and Monthly are not solely listed inside that account menu as the only way to navigate between them

#### Scenario: Current state is exact Today or Monthly only
- **WHEN** an authenticated user opens Add Expense, edit expense, or Profile
- **THEN** neither the Today nor the Monthly primary destination is marked current solely because of that secondary screen

#### Scenario: Shell peers remain Today and Monthly
- **WHEN** an authenticated user opens Today
- **THEN** Today and Monthly remain top-level navigation peers
- **AND** Profile remains reachable from the account menu rather than as a new top-level peer replacing Today or Monthly
- **AND** the navigation brand wordmark links to Today (`home`)
