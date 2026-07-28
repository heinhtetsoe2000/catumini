## ADDED Requirements

### Requirement: Supported display languages
The system SHALL support **Display language** values `en` (English) and `my` (Burmese). All user-facing UI strings except excluded content MUST render in the active display language.

#### Scenario: English is the default fallback
- **WHEN** browser preference does not indicate Burmese and no cookie or profile value is set
- **THEN** the application locale is `en`

#### Scenario: Burmese is selectable
- **WHEN** the Owner or guest selects `MY` on the language toggle
- **THEN** the application locale becomes `my` for that request and subsequent requests until changed

### Requirement: Display language persists on Owner account
The system SHALL store the Owner's **Display language** on the User record and apply it on every authenticated request across devices.

#### Scenario: Authenticated Owner sees saved language
- **WHEN** an authenticated Owner with `display_language` `my` opens Today
- **THEN** UI strings render in Burmese

#### Scenario: Language toggle updates profile immediately
- **WHEN** an authenticated Owner selects `MY` on Profile
- **THEN** the User `display_language` is persisted as `my` without requiring a separate Save action on the profile form

### Requirement: Guest language uses cookie and browser detect
Before login, the system SHALL resolve **Display language** from a remembered cookie when present, otherwise from browser `Accept-Language` (prefer `my` when Burmese is preferred, else `en`), and SHALL write the resolved value to a cookie.

#### Scenario: Login gate respects guest cookie
- **WHEN** a guest with cookie `ledger_locale=my` opens the Login gate
- **THEN** Login gate copy renders in Burmese

#### Scenario: First visit uses browser detect
- **WHEN** a guest with no locale cookie and `Accept-Language` preferring Burmese opens the Login gate
- **THEN** Login gate copy renders in Burmese
- **AND** a locale cookie is set for subsequent guest requests

### Requirement: Language toggle on Profile and Login gate
The system SHALL expose an **EN | MY** segmented toggle on Profile and the Login gate. Activating a segment MUST apply that language immediately (full page reload acceptable).

#### Scenario: Guest toggles language on Login gate
- **WHEN** a guest selects `MY` on the Login gate toggle
- **THEN** the page reloads with Burmese copy
- **AND** the locale cookie is updated

#### Scenario: Owner toggles language on Profile
- **WHEN** an authenticated Owner selects `EN` on Profile
- **THEN** the page reloads with English copy
- **AND** the User `display_language` is updated

### Requirement: Burmese validation and auth messages
When **Display language** is `my`, Laravel validation, authentication, password, and pagination messages MUST use Burmese translations from `lang/my/`.

#### Scenario: Login validation error in Burmese
- **WHEN** a user submits the login form with invalid credentials while locale is `my`
- **THEN** the error message is rendered in Burmese

### Requirement: Burmese typography
When **Display language** is `my`, the system SHALL load Myanmar-capable web fonts for body and titles. When locale is `en`, ledger ink fonts from ADR 0001 MUST remain unchanged.

#### Scenario: Burmese page loads Myanmar fonts
- **WHEN** a page renders with locale `my`
- **THEN** body text uses a Myanmar-capable web font family
- **AND** English locale pages continue using Source Sans 3 and Source Serif 4

### Requirement: Untranslated content exceptions
The system MUST NOT translate `config('app.name')`, the `Ks` amount label, or Owner-entered expense names and descriptions based on **Display language**.

#### Scenario: Ks label stays Latin
- **WHEN** an expense amount is shown with locale `my`
- **THEN** the currency label remains `Ks`

#### Scenario: Expense name stays as entered
- **WHEN** an Owner saved an expense name in English and views it with locale `my`
- **THEN** the expense name text is unchanged

### Requirement: Amounts use Western digits
Expense amounts MUST display with Western digits and grouping in every **Display language**.

#### Scenario: Burmese locale still shows Western digits
- **WHEN** an expense with amount `1500` is shown with locale `my`
- **THEN** the formatted amount uses Western digits (e.g. `1,500`)
