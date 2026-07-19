## Context

Profile is a Livewire Volt page (`pages::user.profile`) with account sections for edit profile, update password, and delete account. Session logout already exists via Breeze: `POST route('logout')` → `AuthenticatedSessionController@destroy` (invalidate session, regenerate CSRF, redirect). Desktop top-nav exposes Log Out inside a Flux dropdown (`sm+` only). Mobile uses a bottom dock to Profile and does not show that dropdown, so logout is unreachable on small screens today.

## Goals / Non-Goals

**Goals:**

- Give every authenticated user a clear Log Out control on the Profile page
- End the session through the existing `logout` route (same behavior as desktop nav)
- Match Profile’s Flux / ledger-ink section patterns
- Prove the flow with a feature test

**Non-Goals:**

- Replacing or removing desktop top-nav Log Out
- Changing delete-account (already logs out then deletes)
- Adding confirmation modals for logout
- NativePHP-specific session or EDGE chrome work
- Restoring a mobile top-nav account menu solely for logout

## Decisions

### 1. Livewire `wire:click="logout"` on the Profile page

- **Choice:** A Flux button with `wire:click="logout"` calling a Livewire method that mirrors Breeze destroy: `Auth::guard('web')->logout()`, `session()->invalidate()`, `session()->regenerateToken()`, then `redirect('/')`.
- **Why:** Keeps Profile account actions consistent with other Livewire handlers on the page (`save`, `updatePassword`, `deleteAccount`); no full-page form POST required.
- **Alternatives considered:** Form POST to `route('logout')` (desktop nav pattern) — rejected so Profile logout stays in the Livewire component.

### 2. Placement as its own Profile section (above Delete Account)

- **Choice:** A dedicated card/section labeled Log Out with short helper text and a full-width or primary action button, placed after Update Password and before Delete Account.
- **Why:** Mirrors existing section rhythm; keeps destructive delete visually last; logout is a common action and should not compete with delete’s danger styling.
- **Alternatives considered:** Icon-only in the header — easy to miss; button beside Edit Profile — mixes session exit with profile editing.

### 3. Visual treatment: filled/subtle, not danger

- **Choice:** Use a non-danger Flux button variant (e.g. `filled` or default), not `danger`.
- **Why:** Logout is reversible (sign in again); danger is reserved for Delete Account.

## Risks / Trade-offs

- [Duplicate logout UI on desktop] → Acceptable: Profile Log Out is the mobile primary path; desktop keeps the menu for discoverability. No requirement to hide either.
- [Users expect a confirm dialog] → Skip for v1; logout is non-destructive. Add later only if feedback asks.
- [Livewire page + full form POST] → Standard; full navigation after logout is desired. Ensure the form is outside conflicting wire:submit parents.

## Migration Plan

- Deploy with the Profile view + test only; no schema or config changes.
- Rollback: revert the Profile section and test; logout route remains for desktop nav.

## Open Questions

- None — reuse of `route('logout')` and section placement are decided above.
