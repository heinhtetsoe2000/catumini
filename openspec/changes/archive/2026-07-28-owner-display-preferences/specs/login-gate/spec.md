## ADDED Requirements

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
