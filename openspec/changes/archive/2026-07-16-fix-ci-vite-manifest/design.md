## Context

The Laravel GitHub Actions workflow (`.github/workflows/laravel.yml`) installs Composer deps on PHP 8.4 and runs `php artisan test`. Feature tests render Blade layouts that call `@vite(['resources/css/app.css', 'resources/js/app.js'])`. Vite’s production manifest lives under `public/build/`, which is gitignored. Local developers usually have a built manifest from `npm run build` / `npm run dev`, so the suite passes locally and fails on a clean CI checkout with `ViteManifestNotFoundException`.

There is no `WithoutVite` usage in `tests/TestCase.php` or `tests/Pest.php` today. Feature tests already extend Pest with `RefreshDatabase` via `tests/Pest.php`.

## Goals / Non-Goals

**Goals:**

- Make Feature/HTTP tests that render `@vite` layouts succeed without `public/build/manifest.json`
- Keep CI fast (no Node/npm install or Vite build for this change)
- Preserve existing Feature assertion behavior (HTML content, redirects, auth flows)

**Non-Goals:**

- Building frontend assets in GitHub Actions
- Changing production Vite configuration or Blade layouts
- Asserting real hashed asset URLs in Feature tests
- Broader CI redesign (caching, matrix, parallel jobs)

## Decisions

### 1. Stub Vite in the test harness (`withoutVite()`), not build assets in CI

- **Choice:** Call Laravel’s `withoutVite()` from the shared Pest/Feature bootstrap so every Feature test that hits a layout gets a no-op Vite facade.
- **Why over npm build in CI:** Feature tests assert page structure and copy, not compiled CSS/JS. Building assets adds Node setup time and flakiness without covering a requirement in this suite.
- **Why over committing `public/build`:** Build artifacts stay gitignored; committing them fights the existing project convention.

**Alternatives considered:**

| Approach | Pros | Cons |
|----------|------|------|
| `npm ci` + `npm run build` in workflow | Closer to production HTML | Slower CI; still doesn’t assert assets; overkill for current tests |
| Commit `public/build` | Zero test code change | Noise in git; stale assets |
| `withoutVite()` in Pest Feature bootstrap | Fast, Laravel-idiomatic, matches test intent | Tests won’t catch missing `@vite` wiring in Blade |

### 2. Apply the stub at Pest Feature scope

- **Choice:** Enable Vite stubbing once for Feature tests (same place `RefreshDatabase` is applied), not per-test.
- **Why:** Almost all Feature files hit Blade layouts; unit tests typically do not need it, and scoping to Feature avoids surprising Unit suite behavior.

### 3. Leave workflow PHP/composer steps as-is; optional env cleanup only

- **Choice:** Do not add Node steps. Optionally remove redundant `DB_CONNECTION` / `DB_DATABASE` overrides from the test step so `phpunit.xml` (`sqlite` + `:memory:`) is the single source of truth.
- **Why:** Those env overrides were not the failure mode; cleaning them reduces future confusion but is secondary to Vite stubbing.

## Risks / Trade-offs

- **[Risk] Tests no longer fail if `@vite` is broken or mis-pointed in Blade** → Mitigation: Acceptable for Feature suite; rely on local `npm run build` / manual UI checks for asset pipeline. Revisit if we add dedicated asset-smoke tests.
- **[Risk] Someone later adds assertions on Vite-generated script/link tags** → Mitigation: Those tests would need real builds or a different fake; document in tasks that content assertions must not depend on hashed asset paths.
- **[Trade-off] CI remains “backend Feature” focused** → Intentional for this change; a future change can add a frontend build job if needed.

## Migration Plan

1. Land `withoutVite()` in Feature test bootstrap.
2. Confirm Feature tests pass with `public/build` absent (simulate clean CI).
3. Push to the branch that runs the Laravel workflow; confirm the “Execute tests” step is green.
4. Rollback: revert the Pest/TestCase change if stubbing causes unexpected assertion failures (unlikely).

## Open Questions

- None blocking. Optional: whether to remove the workflow’s SQLite file env overrides in the same PR (recommended yes, small cleanup).
