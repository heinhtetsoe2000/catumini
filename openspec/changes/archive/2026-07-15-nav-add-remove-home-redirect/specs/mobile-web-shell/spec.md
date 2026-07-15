## MODIFIED Requirements

### Requirement: Standard web navigation is available
The authenticated user SHALL be able to navigate between Today, Monthly (dashboard), and Profile using the web navigation (Breeze or equivalent) without requiring a native shell. Add expense SHALL remain available from the Today page CTA and MUST NOT appear as a primary or responsive top-navigation item.

#### Scenario: Navigate Today to Add expense via Today CTA
- **WHEN** an authenticated user is on Today and follows the Add Expense control on the Today page
- **THEN** they reach the add-expense form

#### Scenario: Navigate to Monthly
- **WHEN** an authenticated user follows the Monthly / dashboard navigation link
- **THEN** they reach the Monthly expense view

#### Scenario: Top navigation has no Add item
- **WHEN** an authenticated user opens Today
- **THEN** the primary navigation does not include an Add (or Add expense) nav item linking to the add-expense route as a top-level peer of Today and Monthly
