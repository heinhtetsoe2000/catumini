## ADDED Requirements

### Requirement: Day headers follow display language
Expense day group headers and relative day labels (including **Today** and **Yesterday**) in Today and Monthly views MUST render in the active **Display language** using the Gregorian calendar in `Asia/Yangon`.

#### Scenario: Today header in Burmese
- **WHEN** an Owner with locale `my` views Today for the current spend date
- **THEN** the current day header uses the Burmese equivalent of Today

#### Scenario: Past day header uses localized month name
- **WHEN** an Owner with locale `my` views a day group for a non-today spend date in the current month
- **THEN** the day header uses a Burmese-localized month name and day (Gregorian, Asia/Yangon)

#### Scenario: Yesterday label in Burmese
- **WHEN** an expense list row represents yesterday's spend date and locale is `my`
- **THEN** the relative label uses the Burmese equivalent of Yesterday

#### Scenario: English day headers unchanged in meaning
- **WHEN** an Owner with locale `en` views Today
- **THEN** the current day header uses the English label Today
- **AND** other day headers use English month abbreviations or names
