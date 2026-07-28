## Purpose

Present the product Login gate at `/` for guests with translatable copy, EN | MY language toggle, and redirect authenticated Owners to Today.

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

### Requirement: Login gate copy is translatable
The Login gate supporting line and Login action label MUST use translation keys so they render in the active **Display language**. The configured product name (`config('app.name')`) MUST remain untranslated.

#### Scenario: Login gate in Burmese
- **WHEN** a guest with locale `my` requests `/`
- **THEN** the supporting line and Login action are rendered in Burmese
- **AND** the configured app name is shown unchanged

#### Scenario: Login gate in English
- **WHEN** a guest with locale `en` requests `/`
- **THEN** the supporting line reads as the English product description equivalent
- **AND** the Login action label is English

### Requirement: Login gate exposes language toggle
The Login gate SHALL include an **EN | MY** segmented language toggle that applies immediately.

#### Scenario: Guest switches to Burmese on Login gate
- **WHEN** a guest on the Login gate selects `MY`
- **THEN** the page reloads with Burmese Login gate copy
- **AND** a locale cookie is set
