## Requirements

### Requirement: Public registration is closed
The system SHALL not allow new users to create accounts through the public registration flow. Login and password reset for existing accounts remain available.

#### Scenario: Register route is unavailable
- **WHEN** an unauthenticated visitor requests the registration page or submits a registration form
- **THEN** the system does not create a user and does not present a successful open sign-up path (e.g. 404 or redirect away from registration)

#### Scenario: Login still works
- **WHEN** an existing user submits valid credentials on the login form
- **THEN** the user is authenticated and can access the expense ledger

### Requirement: Personal account provisioning is intentional
The system SHALL rely on intentional account creation (seed, artisan, or hosted admin path) rather than open self-service registration for the MVP audience of one owner.

#### Scenario: Seeder can create the owner account
- **WHEN** the database seeder (or equivalent intentional create) is run with owner credentials configured
- **THEN** exactly the intended personal account exists and can log in
