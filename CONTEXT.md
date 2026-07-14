# Personal Expense Ledger

A personal money-out journal used on mobile web (iPhone Safari), hosted on Laravel Cloud. NativePHP / store releases are out of scope until Mac access exists.

## Language

**Expense**:
A single money-out entry belonging to the Owner (name, integer amount, optional description, spend date).
_Avoid_: Transaction, purchase, payment (unless meaning money paid out)

**Spend date** (`spent_on`):
The calendar day the money was spent; drives Today and Monthly. Defaults to today; may be backdated.
_Avoid_: Created at, logged at (those are audit timestamps)

**Today**:
The Owner's expenses whose spend date is the current calendar day in Asia/Yangon, plus that day's total.
_Avoid_: Home feed keyed off `created_at`

**Monthly**:
The Owner's expenses for the current calendar month in Asia/Yangon, grouped by spend day, with month total.
_Avoid_: Dashboard analytics, reports, budgets

**Ks**:
The display label for whole-unit currency amounts (integer only; no decimals in MVP).
_Avoid_: MMK symbol variants, multi-currency codes in UI

**Owner**:
The single intentional personal account that uses the ledger. Provisioned by seed or admin path — not open self-registration.
_Avoid_: Tenant, household, team member

**Closed registration**:
Public sign-up is disabled; login and password reset remain for the Owner.
_Avoid_: Invite codes, open register for MVP

**Mobile-web MVP**:
Ship as an HTTPS web app usable on iPhone Safari (optional Add to Home Screen). Not a NativePHP binary or store listing.
_Avoid_: Treating EDGE / `native:*` chrome as required for MVP

## Relationships

- An **Owner** has many **Expenses**
- An **Expense** has exactly one **Spend date**
- **Today** and **Monthly** are views over **Expenses** filtered by **Spend date** in Asia/Yangon
- **Ks** labels the integer **amount** on an **Expense**

## Example dialogue

> **Dev:** "If the Owner logs Monday's lunch on Tuesday night, which day does it count toward?"
> **Domain expert:** "Whatever they set as the **Spend date**. If they pick Monday, it belongs to Monday's **Today** (when that day was current) and that week's **Monthly** day group — not Tuesday's **created_at**."

## Flagged ambiguities

- "Home" in the nav means the **Today** view (`/home`), not a marketing landing page.
- "Dashboard" in the nav means the **Monthly** view, not general analytics.
- "Account" means the **Owner** login identity, not a bank account.
