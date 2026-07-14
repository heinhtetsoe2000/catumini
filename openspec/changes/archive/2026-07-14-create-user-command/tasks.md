## 1. Command

- [x] 1.1 Scaffold Artisan command (`user:create`) via `php artisan make:command`
- [x] 1.2 Implement create flow: required email + password, optional name with non-empty default, hashed password via model cast, set `email_verified_at`
- [x] 1.3 Reject duplicate email and missing/invalid options with non-zero exit and clear error output

## 2. Tests

- [x] 2.1 Add Pest feature/console tests for successful create (with and without name) and login-ready password
- [x] 2.2 Add tests for duplicate email rejection and missing required options
- [x] 2.3 Run the new tests and fix failures; run Pint on dirty PHP files
