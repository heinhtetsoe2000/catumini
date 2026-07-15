## Requirements

### Requirement: Flux Free is the Blade UI kit for in-scope surfaces
The system SHALL use Livewire Flux Free components for interactive and form chrome on in-scope surfaces (Login gate, guest layout consumers in scope, authenticated shell, Today, Monthly, add/edit Expense, Profile). Ledger page logic SHALL remain controller-rendered Blade with classic form submissions; the system MUST NOT require Livewire component classes for those CRUD flows as part of this capability.

#### Scenario: Add expense remains a classic form post
- **WHEN** an authenticated Owner submits the add-expense form with valid data
- **THEN** the request is handled as a normal HTTP form post through the existing route/controller flow
- **AND** the Owner is not required to use a Livewire component action to create the Expense

#### Scenario: In-scope controls use Flux markup
- **WHEN** an authenticated Owner opens Today
- **THEN** the authenticated shell navigation and primary actions are rendered using Flux components (or Flux layout primitives), not the legacy Breeze `x-nav-link` / `x-primary-button` component pair

### Requirement: Livewire is present as Flux runtime only for this change
The application SHALL include Livewire as required by Flux. Introducing Livewire MUST NOT by itself change expense-ledger domain behavior (spend date rules, Today/Monthly filtering, closed registration).

#### Scenario: Today behavior unchanged after Flux install
- **WHEN** an authenticated Owner opens Today after Flux is installed and the shell uses Flux chrome
- **THEN** Today still lists Expenses for the current Asia/Yangon calendar day and shows that day's total
