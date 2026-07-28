## ADDED Requirements

### Requirement: Profile exposes display language toggle
The Profile page SHALL include an **EN | MY** segmented **Display language** toggle that applies immediately without submitting the profile information form.

#### Scenario: Language toggle visible on Profile
- **WHEN** an authenticated Owner opens Profile
- **THEN** the response includes an EN | MY segmented control distinct from the name/email Save form

#### Scenario: Language toggle persists Owner preference
- **WHEN** an authenticated Owner selects `MY` on Profile
- **THEN** the User `display_language` is updated to `my`
- **AND** the page reloads with Burmese UI

### Requirement: Profile exposes appearance toggle
The Profile page SHALL include a **Light | Dark | System** segmented **Appearance** toggle that applies immediately without submitting the profile information form.

#### Scenario: Appearance toggle visible on Profile
- **WHEN** an authenticated Owner opens Profile
- **THEN** the response includes a Light | Dark | System segmented control

#### Scenario: Appearance toggle persists Owner preference
- **WHEN** an authenticated Owner selects `Dark` on Profile
- **THEN** the User `appearance` is updated to `dark`
- **AND** the UI renders in dark mode
