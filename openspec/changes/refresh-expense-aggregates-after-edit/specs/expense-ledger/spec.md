## ADDED Requirements

### Requirement: Expense aggregate totals reflect edits
After an Owner updates an Expense amount, spend totals shown for affected dates and months SHALL reflect the updated amount on subsequent reads, even when those aggregates were calculated before the edit.

#### Scenario: Today total refreshes after an amount edit
- **WHEN** the Owner edits an Expense amount for today after the Today total has already been calculated
- **THEN** the next Today read shows the total using the updated amount

#### Scenario: Monthly totals refresh after an amount edit
- **WHEN** the Owner edits an Expense amount after the Monthly totals have already been calculated
- **THEN** the next Monthly read shows the affected day and month totals using the updated amount