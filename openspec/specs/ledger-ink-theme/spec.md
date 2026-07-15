## Requirements

### Requirement: Shared ledger ink theme across entry and shell
The system SHALL apply one shared visual language (ledger ink) to the Login gate, guest authentication layout, and authenticated application shell: paper-like light surfaces with a paired dark-ink system preference variant, serif styling for product name and major titles, sans styling for body and forms, teal/verdigris primary actions, and wordmark-only branding using `config('app.name')` (no Laravel or NativePHP logo mark as the brand).

#### Scenario: Guest layout shows wordmark not Laravel logo
- **WHEN** an unauthenticated visitor opens the login page
- **THEN** the page presents the configured app name as a text wordmark
- **AND** the page does not use the stock Laravel application logo SVG as the primary brand mark

#### Scenario: Authenticated nav shows wordmark
- **WHEN** an authenticated user opens Today
- **THEN** the navigation presents the configured app name as a text wordmark linking toward Today
- **AND** the navigation does not use the stock Laravel application logo SVG as the primary brand mark

#### Scenario: Login gate uses shared stylesheet theme
- **WHEN** an unauthenticated visitor opens `/`
- **THEN** the Login gate is styled via the application frontend assets (not NativePHP inline starter chrome)
- **AND** primary call-to-action styling uses the teal accent family rather than default NativePHP purple marketing badges

### Requirement: Login gate uses subtle entrance motion only
The Login gate SHALL include subtle entrance motion for the wordmark, supporting line, and Login action. Authenticated shell pages SHALL NOT gain new page-enter motion as part of this capability.

#### Scenario: Expense pages do not require motion
- **WHEN** an authenticated user opens Today
- **THEN** the page functions without depending on entrance animations introduced for the Login gate
