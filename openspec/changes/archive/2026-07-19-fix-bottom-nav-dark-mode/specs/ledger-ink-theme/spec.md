## ADDED Requirements

### Requirement: Elevated shell chrome uses paper surface tokens
Elevated authenticated-shell chrome surfaces (including the mobile bottom navigation dock when present) SHALL use ledger ink paper elevated tokens for backgrounds: light appearance MUST use `paper-elevated` (or equivalent white elevated surface), and dark appearance MUST use `paper-dark-elevated`. Text tokens such as `ink-invert` MUST NOT be used as dark-mode backgrounds for those surfaces. `ink-invert` remains reserved for light text/icon contrast on dark surfaces.

#### Scenario: Mobile bottom dock matches elevated dark surface
- **WHEN** an authenticated Owner views any authenticated shell page in dark appearance on a small viewport where the bottom navigation dock is shown
- **THEN** the dock container background uses the paper-dark-elevated surface token
- **AND** the dock does not use ink-invert as its background

#### Scenario: Mobile bottom dock remains elevated in light appearance
- **WHEN** an authenticated Owner views any authenticated shell page in light appearance on a small viewport where the bottom navigation dock is shown
- **THEN** the dock container background uses the paper-elevated (or white elevated) surface token
