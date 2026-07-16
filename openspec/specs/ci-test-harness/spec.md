## Requirements

### Requirement: Feature tests do not require a Vite build artifact
The test harness SHALL stub Vite asset resolution for Feature tests so HTTP requests that render Blade layouts containing `@vite` succeed without `public/build/manifest.json` present on disk.

#### Scenario: Login gate Feature test without Vite manifest
- **WHEN** Feature tests run on a checkout that has no `public/build/manifest.json`
- **AND** an unauthenticated visitor request to `/` is exercised by a Feature test
- **THEN** the test MUST NOT fail with `ViteManifestNotFoundException`
- **AND** the response MUST still be successful for assertions about page content

#### Scenario: Authenticated layout Feature test without Vite manifest
- **WHEN** Feature tests run on a checkout that has no `public/build/manifest.json`
- **AND** an authenticated request that renders `layouts/app` (or equivalent app shell) is exercised by a Feature test
- **THEN** the test MUST NOT fail with `ViteManifestNotFoundException`

### Requirement: CI Pest suite runs without frontend build steps
The GitHub Actions Laravel test job SHALL be able to complete `php artisan test` successfully without installing Node packages or running `npm run build`, provided Composer dependencies install and the application test bootstrap stubs Vite as required above.

#### Scenario: Workflow test step does not depend on npm
- **WHEN** the Laravel workflow reaches the “Execute tests” step after `composer install` and key generation
- **THEN** the Pest suite MUST pass without prior `npm ci` / `npm install` or `npm run build` steps in that job
