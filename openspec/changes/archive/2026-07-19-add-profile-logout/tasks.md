## 1. Profile Log Out UI

- [x] 1.1 Add a Log Out section on `resources/views/pages/user/⚡profile.blade.php` (after Update Password, before Delete Account) using Flux card/heading/subheading patterns consistent with neighboring sections
- [x] 1.2 Wire a CSRF-protected form that POSTs to `route('logout')` with a non-danger Flux submit button labeled Log Out (mirror desktop nav pattern)

## 2. Tests

- [x] 2.1 Extend `tests/Feature/ProfileTest.php` to assert Profile shows Log Out and that posting logout (via the shared endpoint) ends the session and redirects to `/`
- [x] 2.2 Run `php artisan test --compact tests/Feature/ProfileTest.php` (and Authentication logout test if touched) and confirm green
