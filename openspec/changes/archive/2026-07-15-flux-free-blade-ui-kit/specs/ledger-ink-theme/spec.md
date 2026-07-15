## ADDED Requirements

### Requirement: Flux components honor ledger ink theming
When Flux components are used on Login gate, guest layout, or authenticated shell surfaces, they SHALL inherit the ledger ink visual language (paper/ink surfaces, teal/verdigris primary actions, serif wordmark/titles with sans body) rather than appearing as unthemed stock Flux defaults.

#### Scenario: Primary actions stay teal accent family
- **WHEN** an unauthenticated visitor opens the Login gate or login page after Flux adoption
- **THEN** primary call-to-action styling remains in the teal/verdigris accent family defined by ledger ink
- **AND** the page does not present Flux’s unmodified default primary brand look as the product identity

#### Scenario: Dark mode pairs still apply with Flux chrome
- **WHEN** an authenticated Owner views Today with system dark preference enabled
- **THEN** shell surfaces and Flux chrome use the dark-ink paired tokens (not an unthemed Flux dark default that drops ledger ink)

## MODIFIED Requirements

### Requirement: Shared ledger ink theme across entry and shell
The system SHALL apply one shared visual language (ledger ink) to the Login gate, guest authentication layout, and authenticated application shell: paper-like light surfaces with a paired dark-ink system preference variant, serif styling for product name and major titles, sans styling for body and forms, teal/verdigris primary actions, and wordmark-only branding using `config('app.name')` (no Laravel or NativePHP logo mark as the brand). When Flux Free components are present on those surfaces, they SHALL be themed to this language and MUST NOT replace it with stock Flux aesthetics.

#### Scenario: Guest layout shows wordmark not Laravel logo
- **WHEN** an unauthenticated visitor opens the login page
- **THEN** the page presents the configured app name as a text wordmark
- **AND** the page does not use the stock Laravel application logo SVG as the primary brand mark

#### Scenario: Authenticated nav shows wordmark
- **WHEN** an authenticated user opens Today
- **THEN** the navigation presents the configured app name as a text wordmark linking toward Today
- **AND** the navigation does not use the stock Laravel application logo SVG as the primary brand mark

#### Scenario: Login gate uses shared stylesheet theme
- **WHEN** an unauthenticated visitor opens `/`
- **THEN** the Login gate is styled via the application frontend assets (not NativePHP inline starter chrome)
- **AND** primary call-to-action styling uses the teal accent family rather than default NativePHP purple marketing badges

#### Scenario: Flux chrome does not override ledger ink brand
- **WHEN** an authenticated user opens Today after Flux Free is adopted for the shell
- **THEN** the shell still presents ledger ink paper/ink surfaces and teal primary actions
- **AND** the brand remains the configured app name wordmark (not a Flux or Laravel logo mark)
