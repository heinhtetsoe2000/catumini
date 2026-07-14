## Context

Laravel 12 + Breeze app with a partial personal expense journal (create + Today list + Monthly day groups). Today/Monthly currently filter on `created_at`; amounts are unlabeled integers; timezone is UTC; registration is open; NativePHP EDGE components appear only on Today while the rest is desktop Breeze nav. Owner has no Mac, primary device is iPhone — MVP is **mobile web on Safari**, hosted on **Laravel Cloud**, not NativePHP. Decisions locked in grill-with-docs session (see proposal).

## Goals / Non-Goals

**Goals:**

- Ship a correctable personal expense ledger: create, edit, hard-delete
- Spend date drives Today and Monthly under `Asia/Yangon`
- Integer amounts shown with **Ks**; expenses only (no income)
- Closed registration for a single intentional account
- Web-only shell (strip EDGE); usable on iPhone Safari
- Domain glossary in `CONTEXT.md`
- Test coverage for the new behaviors

**Non-Goals:**

- NativePHP / App Store / Play Store
- Categories, budgets, receipts, reports beyond Monthly day totals
- Soft deletes, multi-currency, decimals, multi-user households
- Income / net worth tracking
- Laravel Cloud provisioning itself (deploy after code is ready)

## Decisions

### 1. Spend date column (`spent_on`)

- **Choice:** Add a `date` column `spent_on` (name TBD in tasks; prefer `spent_on` for clarity). Default to today (Yangon) on create; editable on create/edit forms.
- **Rationale:** Late logging is normal; filtering on `created_at` misattributes spend.
- **Alternatives:** Keep `created_at` (rejected); datetime with time-of-day (unnecessary for MVP day buckets).

### 2. Today / Monthly scopes

- **Choice:** Rewrite `scopeToday` / `scopeMonthly` (and dashboard grouping) to use `spent_on` with `now()` under app timezone `Asia/Yangon`.
- **Rationale:** Single source of truth for calendar boundaries.
- **Alternatives:** Per-user timezone (overkill for solo MVP).

### 3. Edit + hard delete

- **Choice:** Resource-style routes owned by `Expense` authorization via `user_id` match (policy or query scope). Hard `DELETE` — no soft deletes.
- **Rationale:** Mistakes must be fixable; hard delete matches personal ledger simplicity.
- **Alternatives:** Soft delete (recovery later); delete-only without edit (rejected in grill).

### 4. Currency display

- **Choice:** Keep integer DB column; append **`Ks`** in Blade (and any totals). No FX, no decimals.
- **Rationale:** Matches daily Myanmar-style whole units; grill locked label token `Ks`.

### 5. Closed registration

- **Choice:** Disable public register route/UI (return 404 or redirect); allow login. Owner account via seeder / Cloud first-user create / tinker — not open signup.
- **Rationale:** Audience is “just me”; open register invites strangers on a public Cloud URL.
- **Alternatives:** Invite codes (unnecessary); env flag for temporary open register (optional later).

### 6. Strip EDGE for web MVP

- **Choice:** Remove `native:top-bar` / `native:bottom-nav` (and related Native-only chrome) from the expense pages; rely on existing Breeze navigation. Leave `GetDeviceAction` unused or unused by home if it only gates EDGE.
- **Rationale:** EDGE mixed with web nav confuses phone Safari; NativePHP deferred.
- **Alternatives:** Conditional render when Native detected (deferred until native phase).

### 7. Domain docs

- **Choice:** Add root `CONTEXT.md` with glossary from grill (Expense, Spend date, Today, Monthly, Ks, Owner, etc.).
- **Rationale:** No ADRs/CONTEXT existed; grill asked for this before/alongside implementation.

### 8. Empty monthly average

- **Choice:** Guard divide-by-zero on monthly average when count is 0.
- **Rationale:** Current dashboard can blow up on empty months.

## Risks / Trade-offs

- [Existing rows lack `spent_on`] → Migration backfill `spent_on` from `created_at` date (Yangon-aware conversion if needed)
- [Closed register locks out recovery if login broken] → Keep password reset; ensure seed path for owner documented in tasks
- [Stripping EDGE may leave awkward desktop nav on phone] → Prefer touch-friendly Breeze layout polish only as needed for MVP; deep mobile redesign out of scope
- [Timezone change shifts historical “today” meaning] → Accept for personal dogfood; document in CONTEXT

## Migration Plan

1. Ship code + migration to Laravel Cloud (or local first)
2. Run migrations (backfill `spent_on`)
3. Seed/create owner; verify register returns 404
4. Dogfood create → edit → delete → Today/Monthly on iPhone
5. Rollback: reverse migration; re-enable register routes if needed (code revert)

## Open Questions

- Exact column name: `spent_on` vs `expense_date` (default `spent_on` unless prefer otherwise)
- Whether to delete `GetDeviceAction` / `DeviceType` now or leave dead code until native phase (prefer leave unused for now)
