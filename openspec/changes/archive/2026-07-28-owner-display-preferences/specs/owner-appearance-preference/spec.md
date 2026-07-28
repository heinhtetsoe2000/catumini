## ADDED Requirements

### Requirement: Supported appearance modes
The system SHALL support Owner **Appearance** values `light`, `dark`, and `system`. `system` MUST follow OS color-scheme preference via Flux appearance integration.

#### Scenario: System appearance follows OS dark mode
- **WHEN** an Owner's appearance is `system` and the OS prefers dark
- **THEN** the application renders using ledger ink dark tokens

#### Scenario: Light appearance forces light mode
- **WHEN** an Owner's appearance is `light` regardless of OS preference
- **THEN** the application renders using ledger ink light tokens

#### Scenario: Dark appearance forces dark mode
- **WHEN** an Owner's appearance is `dark` regardless of OS preference
- **THEN** the application renders using ledger ink dark tokens

### Requirement: Appearance persists on Owner account
The system SHALL store the Owner's **Appearance** on the User record and apply it on every authenticated request. When unset, the effective appearance MUST be `system`.

#### Scenario: Saved dark appearance applies on Today
- **WHEN** an authenticated Owner with appearance `dark` opens Today on a device whose OS prefers light
- **THEN** the shell renders in dark mode

#### Scenario: Appearance toggle updates profile immediately
- **WHEN** an authenticated Owner selects `Dark` on Profile
- **THEN** the User `appearance` is persisted as `dark` without a separate Save action
- **AND** the UI updates to dark mode without requiring a profile form submit

### Requirement: Guest appearance uses cookie with system default
Before login, the system SHALL resolve **Appearance** from a remembered cookie when present; otherwise MUST use `system`.

#### Scenario: Guest cookie restores appearance
- **WHEN** a guest with cookie `ledger_appearance=dark` opens the Login gate
- **THEN** the Login gate renders in dark mode

### Requirement: Appearance toggle on Profile only
The system SHALL expose a **Light | Dark | System** segmented toggle on Profile only (not on the Login gate).

#### Scenario: Profile shows appearance toggle
- **WHEN** an authenticated Owner opens Profile
- **THEN** the response includes a Light | Dark | System segmented control

#### Scenario: Login gate has no appearance toggle
- **WHEN** a guest opens the Login gate
- **THEN** the response does not include an appearance segmented control

### Requirement: Appearance integrates with Flux
Owner **Appearance** MUST drive Flux appearance state so ledger ink dark/light tokens apply consistently across Flux chrome and custom surfaces.

#### Scenario: Flux toast respects dark appearance
- **WHEN** an Owner with appearance `dark` triggers a Flux toast on Profile
- **THEN** the toast uses dark-appropriate ledger ink styling
