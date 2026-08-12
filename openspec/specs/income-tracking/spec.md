## Purpose

Optional Owner preference for **Income tracking**, Income CRUD, **Available** / **month net**, Income tab, balance strips on Home and History, and aggregate caching per ADR 0008.

## Requirements

### Requirement: Income tracking is an optional Owner preference
The system SHALL store an **Income tracking** boolean on the Owner account, defaulting to off. The Owner SHALL toggle it from **Profile** with instant apply. When off, income UI MUST be absent and **Expense** flows MUST behave as today. When turned off, existing Income rows MUST remain stored.

#### Scenario: Default is tracking off
- **WHEN** a new Owner account is provisioned
- **THEN** **Income tracking** is off
- **AND** no **Income** nav destination or income forms are shown

#### Scenario: Toggle on reveals income UI
- **WHEN** the Owner enables **Income tracking** on Profile
- **THEN** the **Income** nav destination and income features become available before the next navigation without requiring a separate Save action for other profile fields

#### Scenario: Toggle off hides UI but keeps data
- **WHEN** the Owner disables **Income tracking** after logging Income rows
- **THEN** income UI and **Available** strips are hidden
- **AND** Income rows remain in the database
- **AND** re-enabling tracking restores them

### Requirement: Owner can create Income with received date
The system SHALL allow an authenticated Owner with **Income tracking** on to create an Income with name, integer amount, optional description, and a received date defaulting to today in `Asia/Yangon`.

#### Scenario: Create income defaults received date to today
- **WHEN** the Owner submits a valid new Income without overriding the received date
- **THEN** the Income is stored with `received_on` equal to today's date in `Asia/Yangon` and associated to that Owner

#### Scenario: Create income with backdated received date
- **WHEN** the Owner submits a valid Income with a past received date
- **THEN** the Income is stored with that received date

#### Scenario: Cannot create income when tracking off
- **WHEN** **Income tracking** is off
- **THEN** the Owner cannot reach income create flows through the application UI

### Requirement: Owner can edit and hard-delete own Income
The system SHALL allow an authenticated Owner to update and permanently delete Income rows they own, with the same field set as create. Soft deletes MUST NOT be used.

#### Scenario: Edit own income
- **WHEN** the Owner submits valid changes to their Income
- **THEN** the stored Income reflects the new values

#### Scenario: Cannot edit another Owner's income
- **WHEN** an Owner attempts to update Income owned by a different user
- **THEN** the system denies the update

#### Scenario: Delete own income
- **WHEN** the Owner confirms deletion of their Income
- **THEN** the row is removed and no longer appears in the Income list

### Requirement: Available is global carry-over with zero floor until first Income
**Available** SHALL equal total received minus total spent for the Owner (all Income minus all Expense amounts). It MUST NOT reset monthly. When the Owner has zero Income rows, **Available** displayed everywhere MUST be `0`. After the first Income row exists, **Available** MUST reflect honest math, including negative values shown with distinct alarm styling, without blocking Expense entry.

#### Scenario: Available is zero before first income
- **WHEN** **Income tracking** is on, the Owner has Expenses but zero Income rows, and **Available** is shown
- **THEN** the displayed **Available** is `0`

#### Scenario: Available updates after first income
- **WHEN** the Owner creates their first Income of amount `500000` and has Expenses totaling `100000`
- **THEN** **Available** displays `400000` Ks

#### Scenario: Negative available shows alarm styling
- **WHEN** the Owner has at least one Income row and total spent exceeds total received
- **THEN** **Available** displays a negative integer with alarm styling
- **AND** the Owner can still create Expenses

### Requirement: Month net is current-month informational
On the **Income** tab, the system SHALL show current calendar month in `Asia/Yangon`: month received, month spent, and **month net** (month received minus month spent). **Month net** MUST NOT be used as the global **Available** amount.

#### Scenario: Month net on income tab
- **WHEN** the Owner views the **Income** tab during a month with Income and Expense activity
- **THEN** the month row shows received, spent, and month net for that calendar month only

### Requirement: Income tab content and navigation
When **Income tracking** is on, the system SHALL provide an **Income** primary nav destination between **Home** and **History**. The **Income** view SHALL show the month row, **Available** row, all-time Income list (newest first), and add-income control. On viewports below `sm`, the nav control MUST be icon-only with accessible name **Income**; on `sm` and larger it MUST use the text label **Income**.

#### Scenario: Income nav hidden when tracking off
- **WHEN** **Income tracking** is off
- **THEN** primary navigation does not include an **Income** destination

#### Scenario: Income nav visible when tracking on
- **WHEN** **Income tracking** is on and the Owner opens **Home**
- **THEN** an **Income** primary destination appears between **Home** and **History**

#### Scenario: Income list is all-time
- **WHEN** the Owner views the **Income** tab with Income rows from prior months
- **THEN** all Income rows for that Owner are listed, not only the current month

### Requirement: Balance strips on Home and History when tracking on
When **Income tracking** is on, **Home** SHALL show a compact strip with today's spent total and global **Available**. **History** SHALL show a compact strip with current-month received, current-month spent, and global **Available**. When tracking is off, these strips MUST NOT appear.

#### Scenario: Home strip when tracking on
- **WHEN** **Income tracking** is on and the Owner views **Home**
- **THEN** a strip shows today's spent and **Available**

#### Scenario: History strip when tracking on
- **WHEN** **Income tracking** is on and the Owner views **History**
- **THEN** a strip shows current-month received, current-month spent, and **Available**

#### Scenario: No strips when tracking off
- **WHEN** **Income tracking** is off
- **THEN** **Home** and **History** do not show **Available** or income totals

### Requirement: Available aggregates are cached per Owner
The system SHALL cache Owner-scoped totals for total received, total spent, and derived **Available**, and current-month income totals, using lazy fill and immediate invalidation on Income and Expense writes, consistent with ADR 0008.

#### Scenario: Income write busts available cache
- **WHEN** the Owner creates, updates, or deletes an Income
- **THEN** cached totals used for **Available** for that Owner are invalidated before the response completes

#### Scenario: Expense write busts available cache
- **WHEN** the Owner creates, updates, or deletes an Expense while **Income tracking** is on
- **THEN** cached totals used for **Available** for that Owner are invalidated before the response completes

### Requirement: Income amounts use integer Ks
Income amounts SHALL be stored as integers and displayed with the **Ks** label, matching **Expense** amount rules.

#### Scenario: Display income amount with Ks
- **WHEN** an Income with amount `350000` is shown
- **THEN** the UI includes the formatted integer and the label `Ks`
