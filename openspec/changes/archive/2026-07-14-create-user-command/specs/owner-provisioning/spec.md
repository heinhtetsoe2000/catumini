## ADDED Requirements

### Requirement: Create Owner via Artisan with email and password
The system SHALL provide an Artisan command that creates a User when given a valid unique email and a password. An optional name MAY be supplied; when omitted, the system MUST set a non-empty default name. The password MUST be stored hashed. Public registration remains closed; this command is an intentional provisioning path.

#### Scenario: Successful create with email and password
- **WHEN** an operator runs the user-create command with a new email and a password
- **THEN** a User exists with that email, can authenticate with that password, and the command exits successfully with confirmation

#### Scenario: Optional name is applied
- **WHEN** an operator runs the user-create command with email, password, and a name
- **THEN** the created User has that name

#### Scenario: Default name when name omitted
- **WHEN** an operator runs the user-create command with email and password but no name
- **THEN** the created User has a non-empty name

### Requirement: Reject invalid or duplicate create attempts
The system MUST NOT create a User when the email is already taken or when required credentials are missing/invalid. The command MUST exit with a non-success status and a clear error message.

#### Scenario: Duplicate email is rejected
- **WHEN** an operator runs the user-create command with an email that already belongs to a User
- **THEN** no new User is created and the command reports that the email is already in use

#### Scenario: Missing required credentials
- **WHEN** an operator runs the user-create command without an email or without a password
- **THEN** no User is created and the command fails with a validation or usage error
