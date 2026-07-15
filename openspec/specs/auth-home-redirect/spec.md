## Requirements

### Requirement: Default post-auth destination is Today
When an Owner authenticates successfully (or completes an auth flow that uses the application’s default intended destination), the system SHALL redirect to Today (`home`) unless a prior intended URL is present.

#### Scenario: Successful login goes to Today
- **WHEN** an Owner submits valid credentials on the login form with no prior intended URL
- **THEN** they are redirected to the Today (`home`) route

#### Scenario: Email verification completion defaults to Today
- **WHEN** an Owner completes email verification and there is no prior intended URL
- **THEN** they are redirected to the Today (`home`) route (query parameters for verified status may still be present)
