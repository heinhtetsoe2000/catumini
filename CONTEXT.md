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

**Login gate**:
The public entry for guests before authentication: product name, one short line stating this is a personal expense ledger, and a Login action — no marketing, registration, framework promo, or logo mark.
_Avoid_: Welcome page, marketing landing, treating `/` as Home/Today

## Relationships

- An **Owner** has many **Expenses**
- An **Expense** has exactly one **Spend date**
- **Today** and **Monthly** are views over **Expenses** filtered by **Spend date** in Asia/Yangon
- **Ks** labels the integer **amount** on an **Expense**
- A guest reaches the ledger through the **Login gate**; an authenticated **Owner** opening `/` goes to **Today**

## Example dialogue

> **Dev:** "If the Owner logs Monday's lunch on Tuesday night, which day does it count toward?"
> **Domain expert:** "Whatever they set as the **Spend date**. If they pick Monday, it belongs to Monday's **Today** (when that day was current) and that week's **Monthly** day group — not Tuesday's **created_at**."

> **Dev:** "Is the page at `/` the Home screen?"
> **Domain expert:** "No — that's the **Login gate**. **Home** in the nav is **Today**. If you're already signed in, `/` should take you straight to **Today**."

## Flagged ambiguities

- "Home" in the nav means the **Today** view (`/home`), not a marketing landing page or the **Login gate**.
- "Welcome" / default Laravel-NativePHP splash means the **Login gate** after this change — not a product pitch or framework starter screen.
- "Dashboard" in the nav means the **Monthly** view, not general analytics.
- Mobile shell may show calendar-day / calendar-month icons for **Today** / **Monthly**; labels and `aria-label`s stay those glossary names — never house (“Home”) or chart (“Dashboard analytics”) icons.
- "Account" means the **Owner** login identity, not a bank account.
- On-screen product name is `APP_NAME` / `config('app.name')`, not a hard-coded marketing name (e.g. avoid treating "Mimi" as the glossary brand).
- Visual system decisions live in `docs/adr/0001-ledger-ink-visual-system.md`, not in this glossary.
- UI component kit (Flux Free on Blade controllers) lives in `docs/adr/0002-flux-free-blade-ui-kit.md`, not in this glossary.
- Mobile always-visible **Today** / **Monthly** top destinations (icons on small screens, text on desktop; no hamburger) live in `docs/adr/0003-mobile-top-primary-destinations.md`, not in this glossary.
- “Public launch later” means a future multi-user phase (many ledger accounts, registration model TBD) — it does **not** change the current MVP: one **Owner**, **Closed registration**. Do not redefine **Owner** until that phase is designed.
