## Why

GitHub Actions reaches the Pest step but Feature tests that render Blade layouts fail with `ViteManifestNotFoundException`, because `/public/build` is gitignored and the workflow never builds frontend assets. Locally tests pass only because a prior `npm run build` left a manifest on disk. CI cannot reliably green until tests no longer depend on that artifact (or CI builds it).

## What Changes

- Disable Vite asset resolution in the Pest/Feature test bootstrap (`withoutVite()`), so HTTP tests that render layouts do not require `public/build/manifest.json`
- Keep the Laravel workflow on PHP 8.4 and `composer install` (already fixed); do **not** add an npm build to CI for this change unless we later need asset-URL assertions
- Optionally tighten workflow env so test DB settings defer to `phpunit.xml` (`:memory:`) instead of a file SQLite path that adds no value
- Verify Feature tests that hit `@vite` layouts pass without a local/CI Vite manifest

## Capabilities

### New Capabilities

- `ci-test-harness`: Test and CI harness behavior so Feature/HTTP tests run without a Vite build artifact on clean checkouts

### Modified Capabilities

- (none)

## Impact

- `tests/Pest.php` and/or `tests/TestCase.php` (Vite stubbing)
- Possibly `.github/workflows/laravel.yml` (env cleanup only; no Node job in this change)
- Feature tests that render `welcome`, `layouts/app`, or `layouts/guest` (login gate, auth pages, shell, expense ledger, profile, etc.)
- No application runtime or production asset pipeline changes
