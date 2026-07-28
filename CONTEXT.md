# Personal Expense Ledger

A personal money-out journal used on mobile web (iPhone Safari), hosted on Laravel Cloud. NativePHP / store releases are out of scope until Mac access exists.

## Language

**Expense**:
A single money-out entry belonging to the Owner (name, integer amount, optional description, spend date).
_Avoid_: Transaction, purchase, payment (unless meaning money paid out)

**Expense identity**:
The permanent identifier assigned when an Expense is first recorded on any device; unchanged when synced to the server.
_Avoid_: Local id, server id, row number, temporary id

**Owner identity**:
The permanent identifier assigned when an Owner account is provisioned; unchanged for the life of the account.
_Avoid_: Session id, login token, row number

**Spend date** (`spent_on`):
The calendar day the money was spent; drives Today and Monthly. Defaults to today; may be backdated.
_Avoid_: Created at, logged at (those are audit timestamps)

**Today**:
The Owner's expenses whose spend date is the current calendar day in Asia/Yangon, plus that day's total. Day headers and relative labels (e.g. yesterday) follow **Display language**; the underlying calendar stays Gregorian in Asia/Yangon.
_Avoid_: Home feed keyed off `created_at`, Buddhist calendar in MVP

**Monthly**:
The Owner's expenses for the current calendar month in Asia/Yangon, grouped by spend day, with month total.
_Avoid_: Dashboard analytics, reports, budgets

**Ks**:
The display label for whole-unit currency amounts (integer only; no decimals in MVP). Amounts use Western digits with grouping in every **Display language**.
_Avoid_: MMK symbol variants, multi-currency codes in UI, Myanmar numerals in MVP

**Owner**:
The single intentional personal account that uses the ledger. Provisioned by seed or admin path — not open self-registration.
_Avoid_: Tenant, household, team member

**Closed registration**:
Public sign-up is disabled; login and password reset remain for the Owner.
_Avoid_: Invite codes, open register for MVP

**Display language**:
The language the ledger UI is shown in. Supported: English (`en`) and Burmese (`my`). Chosen by the Owner and saved on their account; same language on every device after login. If the Owner has never chosen one, the first visit follows browser preference until an explicit choice or first login persists it. Covers in-app UI and Laravel validation/auth messages; does not translate **APP_NAME**, the **Ks** label, Owner-entered expense text (name, description), or system emails in MVP.
_Avoid_: Locale (implementation term), i18n, translation file, per-device language without an Owner override

**Appearance**:
How the ledger looks: Light, Dark, or System (follow OS). Chosen by the Owner from **Profile** using a **Light | Dark | System** segmented toggle; System is the default when unset.
_Avoid_: Theme (implementation term), color mode, night mode

**Mobile-web MVP**:
Ship as an HTTPS web app usable on iPhone Safari (optional Add to Home Screen). Not a NativePHP binary or store listing.
_Avoid_: Treating EDGE / `native:*` chrome as required for MVP

**Login gate**:
The public entry for guests before authentication: product name, one short line stating this is a personal expense ledger, and a Login action — no marketing, registration, framework promo, or logo mark.
_Avoid_: Welcome page, marketing landing, treating `/` as Home/Today

## Relationships

- An **Owner** has many **Expenses**
- An **Expense** belongs to exactly one **Owner** (required; no orphan expenses)
- An **Expense** receives its **Expense identity** at first capture; sync does not reassign it
- An **Owner** receives its **Owner identity** at provision; it does not change
- An **Expense** has exactly one **Spend date**
- **Today** and **Monthly** are views over **Expenses** filtered by **Spend date** in Asia/Yangon
- **Ks** labels the integer **amount** on an **Expense**
- A guest reaches the ledger through the **Login gate**; an authenticated **Owner** opening `/` goes to **Today**
- Before login, **Display language** may follow browser preference or a remembered cookie; after login, the Owner's saved **Display language** on their account is canonical
- Before login, **Appearance** may follow a remembered cookie or OS; after login, the Owner's saved **Appearance** on their account is canonical; default is **System** when unset
- The Owner changes **Display language** from **Profile** or the **Login gate**, using a compact **EN | MY** segmented toggle; changes apply immediately
- The Owner changes **Appearance** from **Profile** only (Light, Dark, or System); changes apply immediately

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
- When **Display language** is Burmese, body and title fonts switch to Myanmar-capable web fonts; English keeps the ledger ink typefaces from ADR 0001.
- UI component kit (Flux Free on Blade controllers) lives in `docs/adr/0002-flux-free-blade-ui-kit.md`, not in this glossary.
- Mobile always-visible **Today** / **Monthly** top destinations (icons on small screens, text on desktop; no hamburger) live in `docs/adr/0003-mobile-top-primary-destinations.md`, not in this glossary.
- **Expense** aggregate caching (Owner-scoped day/month rollups, not lists) lives in `docs/adr/0004-owner-scoped-expense-aggregate-cache.md`, not in this glossary.
- **Owner identity** and **Expense identity** (stable, client-authoritative, UUID v7 in implementation) live in `docs/adr/0005-uuid-v7-domain-identities.md`, not in this glossary.
- **Display language** and **Appearance** (Owner-scoped preferences, guest cookie bridge, shipping order) live in `docs/adr/0006-owner-display-preferences.md`, not in this glossary.
- “Public launch later” means a future multi-user phase (many ledger accounts, registration model TBD) — it does **not** change the current MVP: one **Owner**, **Closed registration**. Do not redefine **Owner** until that phase is designed.
- **Display language** ships before **Appearance**; they share the same Owner-preference storage pattern but are separate releases.
