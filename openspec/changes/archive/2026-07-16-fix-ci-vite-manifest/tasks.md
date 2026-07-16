## 1. Test harness

- [x] 1.1 Enable Laravel `withoutVite()` for Feature tests in the shared Pest bootstrap (`tests/Pest.php`) or base `TestCase` so all Feature HTTP tests stub Vite
- [x] 1.2 Confirm Unit tests remain unaffected (no unnecessary Vite stubbing required unless they render views)

## 2. Workflow cleanup (optional but recommended)

- [x] 2.1 Remove redundant `DB_CONNECTION` / `DB_DATABASE` env overrides from the Laravel workflow test step so `phpunit.xml` (`sqlite` + `:memory:`) is authoritative
- [x] 2.2 Leave PHP 8.4 and `composer install` steps unchanged; do not add npm/Node build steps

## 3. Verification

- [x] 3.1 Run Feature tests that hit `@vite` layouts (e.g. LoginGate, Authentication, MobileWebShell) with `public/build` temporarily absent and confirm they pass
- [x] 3.2 Run the full Pest suite (`php artisan test --compact`) and confirm all tests pass
- [ ] 3.3 After push, confirm the GitHub Actions “Execute tests” step succeeds without a Vite manifest or npm build
