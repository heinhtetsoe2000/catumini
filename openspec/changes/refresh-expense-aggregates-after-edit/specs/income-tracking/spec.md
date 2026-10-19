## MODIFIED Requirements

### Requirement: Available aggregates are cached per Owner
The system SHALL cache Owner-scoped totals for total received, total spent, and derived **Available**, and current-month income totals, using lazy fill and immediate invalidation on Income and Expense writes, consistent with ADR 0008. Updating an Expense amount MUST invalidate cached total spent and **Available** values for that Owner before the update response completes.

#### Scenario: Income write busts available cache
- **WHEN** the Owner creates, updates, or deletes an Income
- **THEN** cached totals used for **Available** for that Owner are invalidated before the response completes

#### Scenario: Expense write busts available cache
- **WHEN** the Owner creates, updates, or deletes an Expense while **Income tracking** is on
- **THEN** cached totals used for **Available** for that Owner are invalidated before the response completes

#### Scenario: Expense amount edit refreshes available
- **WHEN** the Owner updates an Expense amount while **Income tracking** is on and the Owner's total spent has already been cached
- **THEN** the cached total spent and **Available** values for that Owner are invalidated before the response completes
- **AND** the next read shows **Available** calculated using the updated Expense amount