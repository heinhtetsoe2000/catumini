## ADDED Requirements

### Requirement: Owner appearance overrides system preference
Ledger ink dark and light token pairs MUST respond to Owner **Appearance** (`light`, `dark`, `system`), not only OS system preference. When appearance is `system`, behavior MUST match the existing system-preference dark-ink pairing from ADR 0001.

#### Scenario: Owner forced light ignores OS dark
- **WHEN** an Owner with appearance `light` views Today while OS prefers dark
- **THEN** shell surfaces use ledger ink light paper tokens
- **AND** Flux chrome matches the light ledger ink theme

#### Scenario: Owner system appearance matches prior ADR behavior
- **WHEN** an Owner with appearance `system` views Today and OS prefers dark
- **THEN** shell and Flux chrome use the dark-ink paired tokens as before this change

### Requirement: Locale-specific typography preserves ledger ink for English
When **Display language** is `en`, ledger ink typography (Source Sans 3, Source Serif 4) MUST remain as defined. When **Display language** is `my`, Myanmar-capable web fonts MUST replace sans and serif stacks without altering ledger ink color tokens.

#### Scenario: English typography unchanged
- **WHEN** an Owner views Today with locale `en`
- **THEN** body and title fonts remain Source Sans 3 and Source Serif 4

#### Scenario: Burmese typography uses Myanmar web fonts
- **WHEN** an Owner views Today with locale `my`
- **THEN** body and title fonts use loaded Myanmar-capable web fonts
- **AND** paper/ink/accent color tokens remain ledger ink
