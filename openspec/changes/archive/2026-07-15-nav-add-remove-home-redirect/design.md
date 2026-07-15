## Context

Shell navigation currently lists Today, Add, and Monthly. Today already exposes an “Add Expense” button in the page header, so the nav Add link is redundant. After login (and several other Breeze intended redirects), controllers still point at `route('dashboard')` (Monthly) instead of `route('home')` (Today), which conflicts with treating Today as the Owner’s daily entry point.

## Goals / Non-Goals

**Goals:**

- Remove Add from primary and responsive navigation only
- Default auth intended destination to Today (`home`)
- Update specs and tests to match

**Non-Goals:**

- Removing or renaming the `/add-expense` route or form
- Removing the Today header Add Expense CTA
- Changing Monthly as a destination or renaming dashboard route
- Forcing redirects when `url.intended` points elsewhere (preserve Laravel `redirect()->intended()` behavior)

## Decisions

1. **Nav only removes Add** — Keep Today header CTA as the single primary path to create expenses from the ledger home.  
   *Rejected:* Removing the header button too (User asked for top layout only).

2. **Replace `route('dashboard')` defaults with `route('home')`** in Breeze auth controllers that use intended redirects after login/verification/confirm password. Grep and update all application (non-vendor) occurrences used as post-auth defaults. Leave explicit Monthly navigation and dashboard route tests that assert Monthly behavior.  
   *Rejected:* Alias `/dashboard` to home (would break Monthly).

3. **Tests** — Authentication and verification feature tests assert redirect to `home`. Mobile shell test still may assert Today CTA links to add-expense, but must not require an Add nav link labeled “Add”.

## Risks / Trade-offs

- **[Risk]** Missed `dashboard` redirect in an obscure auth controller → **Mitigation:** Project-wide grep for `route('dashboard'` in `app/` and `tests/`  
- **[Trade-off]** Add expense is one more tap from Monthly (back to Today first) → Acceptable; Owner’s primary flow is Today

## Migration Plan

- Deploy with normal web release; no DB changes  
- Rollback: restore nav link and dashboard redirect targets

## Open Questions

- None
