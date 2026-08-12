## REMOVED Requirements

### Requirement: Expenses only
**Reason**: Income is now an optional capability (`income-tracking`) toggled per Owner; the expense ledger remains the default experience when tracking is off.
**Migration**: No data migration. Remove the requirement and scenario from the deployed spec when this change archives; expense CRUD behavior is unchanged.

#### Scenario: No income entry path
- **WHEN** a user uses the MVP expense flows
- **THEN** there is no UI or API for recording income
