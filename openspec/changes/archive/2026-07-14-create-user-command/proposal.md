## Why

Public registration is closed, so the Owner account must be provisioned intentionally. A dedicated Artisan command lets us create (or replace) a login with email and password without relying on seeders alone or reopening self-registration — especially useful on Laravel Cloud and local resets.

## What Changes

- Add an Artisan command that creates a user from email and password (with optional name)
- Validate uniqueness and required credentials; hash the password via the User model
- Cover the command with feature/console tests
- Leave public registration closed; this is the intentional admin path alongside the existing seeder

## Capabilities

### New Capabilities
- `owner-provisioning`: Create the Owner account via an Artisan command using email and password (and optional name), with clear success/failure feedback

### Modified Capabilities
- (none — closed registration requirements stay the same; this implements the intentional provisioning path already described there)

## Impact

- New console command under `app/Console/Commands/`
- `User` model / factory usage for persistence
- Pest console/feature tests
- Ops workflow: run the command locally or on Laravel Cloud instead of (or in addition to) `db:seed` for account creation
