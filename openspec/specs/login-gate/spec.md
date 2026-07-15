## Requirements

### Requirement: Login gate presents product entry for guests
The system SHALL present a Login gate at `/` for unauthenticated visitors that shows the configured product name (`config('app.name')`), the exact supporting line “Your personal expense ledger.”, and a Login action that navigates to the existing login route — without registration, NativePHP or framework promotional links, or a logo graphic.

#### Scenario: Guest sees Login gate content
- **WHEN** an unauthenticated visitor requests `/`
- **THEN** the response is successful and includes the configured app name, the text “Your personal expense ledger.”, and a link to the login route
- **AND** the response does not include NativePHP documentation, Discord, GitHub, or Bifrost promotional links
- **AND** the response does not include a registration CTA

#### Scenario: Guest Login CTA goes to login
- **WHEN** an unauthenticated visitor follows the Login action on the Login gate
- **THEN** they reach the login page

### Requirement: Authenticated visitors skip the Login gate
When an authenticated Owner requests `/`, the system SHALL redirect them to Today (`home`) rather than rendering the Login gate.

#### Scenario: Authenticated Owner visiting slash lands on Today
- **WHEN** an authenticated user requests `/`
- **THEN** the response redirects to the Today (`home`) route
