## ADDED Requirements

### Requirement: Owner identity is UUID v7 at provision
The system SHALL assign a UUID version 7 string as **Owner identity** (`users.id`) when an Owner is provisioned via Artisan command, seeder, or factory. The identity MUST NOT change for the life of the account.

#### Scenario: Artisan create assigns UUID v7
- **WHEN** an operator successfully runs the user-create command
- **THEN** the created User has a UUID v7 primary key

#### Scenario: Owner identity is stable after provision
- **WHEN** the Owner updates profile fields or authenticates after provision
- **THEN** the User primary key is unchanged from the value assigned at provision
