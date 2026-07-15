## Why

The top navigation duplicates Add expense (already available as the primary CTA on Today), and Breeze still sends the Owner to Monthly (`dashboard`) after login instead of the daily entry point **Today**. Tighten navigation and auth redirects so the phone MVP opens where logging spend starts.

## What Changes

- Remove the **Add** nav link from desktop and mobile shell navigation (Today header “Add Expense” remains)
- Change default post-auth redirects from `dashboard` (Monthly) to `home` (Today) across Breeze auth controllers and related tests
- Keep Monthly reachable via the Monthly nav link; add-expense route/page unchanged

## Capabilities

### New Capabilities

- `auth-home-redirect`: After successful authentication (and related intended redirects that currently target dashboard), the Owner is sent to Today (`home`) unless a prior intended URL applies

### Modified Capabilities

- `mobile-web-shell`: Primary web navigation is Today, Monthly, and Profile — not Add expense in the top/responsive nav; Add expense is reached from the Today page CTA

## Impact

- Views: `layouts/navigation.blade.php` (desktop + responsive Add links)
- Auth controllers: `AuthenticatedSessionController`, email verification / confirm password (and any remaining `route('dashboard')` intended defaults)
- Tests: `AuthenticationTest`, `EmailVerificationTest`, `CreateUserCommandTest`, `MobileWebShellTest` (nav expectations)
- Spec language for shell navigation
