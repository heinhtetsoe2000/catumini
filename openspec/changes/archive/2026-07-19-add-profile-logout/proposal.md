## Why

On small viewports the top-nav account dropdown (with Log Out) is hidden, and the mobile bottom dock only reaches Profile. Authenticated mobile users therefore have no way to end their session without desktop chrome. The Profile page already owns account actions (edit, password, delete); Log Out belongs there too.

## What Changes

- Add a **Log Out** action on the Profile Livewire page (`pages::user.profile`)
- Reuse session teardown behavior equivalent to Breeze logout (invalidate + regenerate token) inside a Livewire `logout` action triggered by `wire:click`
- Style the control with Flux Free / ledger ink so it fits the existing Profile account sections
- Cover logout-from-profile with a feature test (Livewire `logout` ends the session and redirects to `/`)
- Out of scope: changing desktop top-nav Log Out, delete-account flow, or NativePHP session handling

## Capabilities

### New Capabilities

- `profile-logout`: Profile page exposes a Log Out control (`wire:click`) that ends the authenticated session and redirects to `/`

### Modified Capabilities

- (none)

## Impact

- View: `resources/views/pages/user/⚡profile.blade.php` (UI + optional Livewire logout method vs form POST)
- Routes/controllers: reuse `route('logout')` / `AuthenticatedSessionController@destroy` — no new auth endpoints
- Tests: `tests/Feature/ProfileTest.php` (and optionally assert Log Out is present on profile in shell tests)
- No dependency or migration changes
