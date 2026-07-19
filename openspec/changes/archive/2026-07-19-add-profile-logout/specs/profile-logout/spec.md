## ADDED Requirements

### Requirement: Profile page exposes Log Out
The Profile page SHALL provide a Log Out control that authenticated users can activate without using the top-navigation account menu. Activating Log Out SHALL invoke a Livewire action via `wire:click`, end the authenticated session (logout, invalidate session, regenerate CSRF token), and redirect the user to `/`.

#### Scenario: Log Out control is visible on Profile
- **WHEN** an authenticated user opens the Profile page
- **THEN** the response includes a Log Out control bound with `wire:click` to the logout action

#### Scenario: User logs out from Profile
- **WHEN** an authenticated user activates Log Out on the Profile page
- **THEN** the user becomes a guest
- **AND** they are redirected to `/`
