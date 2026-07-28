## Purpose

Profile page for authenticated Owners: account settings, Log Out, Display language toggle, and Appearance toggle with instant apply.

## Requirements

### Requirement: Profile page exposes Log Out
The Profile page SHALL provide a Log Out control that authenticated users can activate without using the top-navigation account menu. Activating Log Out SHALL invoke a Livewire action via `wire:click`, end the authenticated session (logout, invalidate session, regenerate CSRF token), and redirect the user to `/`.

#### Scenario: Log Out control is visible on Profile
- **WHEN** an authenticated user opens the Profile page
- **THEN** the response includes a Log Out control bound with `wire:click` to the logout action

#### Scenario: User logs out from Profile
- **WHEN** an authenticated user activates Log Out on the Profile page
- **THEN** the user becomes a guest
- **AND** they are redirected to `/`

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
