## 1. Navigation

- [x] 1.1 Remove the Add / add-expense nav links from desktop and responsive sections in `layouts/navigation.blade.php`
- [x] 1.2 Confirm Today header still links to add-expense; Monthly and Profile nav remain

## 2. Auth redirects

- [x] 2.1 Change default intended redirects from `route('dashboard')` to `route('home')` in Breeze auth controllers (login, verify email, confirm password, and any other app redirects using dashboard as default)
- [x] 2.2 Grep `app/` for remaining post-auth `dashboard` defaults and fix any misses

## 3. Tests

- [x] 3.1 Update auth feature tests (`AuthenticationTest`, email verification, related) to expect redirect to `home`
- [x] 3.2 Update `MobileWebShellTest` (and any nav assertions) so Add is via Today CTA, not a top-nav peer of Today/Monthly
- [x] 3.3 Run affected Pest tests and Pint on dirty PHP
