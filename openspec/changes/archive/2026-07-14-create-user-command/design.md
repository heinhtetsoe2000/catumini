## Context

Closed registration means the Owner cannot self-sign up. Today `DatabaseSeeder` provisions the Owner from `OWNER_EMAIL` / `OWNER_PASSWORD` / `OWNER_NAME` env vars. That works for bootstrapping, but operators also need a first-class Artisan path to create a user with explicit email and password (e.g. production on Laravel Cloud without running the full seeder, or local ad-hoc accounts).

The `User` model already fillable-casts `name`, `email`, and hashed `password`. No new auth packages or schema changes are required.

## Goals / Non-Goals

**Goals:**

- Provide `php artisan user:create` (or equivalent) that accepts email + password (and optional name) and persists a login-ready User
- Fail clearly on duplicate email or invalid input
- Test the happy path and validation/duplicate failures

**Non-Goals:**

- Reopening public registration
- Multi-user roles, invites, or team membership
- Updating existing users’ passwords (password reset / profile remain the paths)
- Changing the existing DatabaseSeeder contract (it can stay; the command is an additional intentional path)

## Decisions

1. **Artisan command over tinker scripts**  
   A named command is discoverable, scriptable on Laravel Cloud, and easy to test with `artisan()`.  
   *Alternatives considered:* only seeder (already exists; awkward for one-off credential args); Filament/admin UI (out of MVP scope).

2. **Signature: required `--email` / `--password`, optional `--name`**  
   Match the user request (email + password). Default name to something sensible (e.g. part of email local-part or `Owner`) when omitted so `name` remains non-null.  
   *Alternatives considered:* interactive Prompts-only input (nice UX but harder for non-interactive Cloud runs — support options first; prompts optional later).

3. **Create-only; reject duplicate email**  
   Prefer fail-loud over silent update so production credentials are not overwritten by accident.  
   *Alternatives considered:* `updateOrCreate` like the seeder (convenient but dangerous if mistyped email overlaps).

4. **Hash via model cast**  
   Pass the plain password into `User::create` / `User::query()->create` and rely on `'password' => 'hashed'` rather than double-hashing with `Hash::make`.

5. **Mark email verified on create**  
   Align with seeder behavior so the Owner is not blocked by email verification if enabled later.  
   *Alternative:* leave unverified — unnecessary friction for intentional admin create.

## Risks / Trade-offs

- **[Risk] Password appears in shell history / process list when passed as an option** → Prefer documenting that Cloud/CI should use secure env injection; optional future improvement: prompt for password with hidden input when `--password` is omitted.
- **[Risk] Anyone with Artisan/SSH access can create accounts** → Acceptable for single-Owner personal app; no web exposure.
- **[Trade-off] Create-only vs updateOrCreate** → Safer; password changes stay on reset/profile flows.

## Migration Plan

1. Ship the command and tests.
2. On Laravel Cloud / local: `php artisan user:create --email=... --password=...` after migrate.
3. Rollback: delete the command class and tests; remove any created User via tinker/DB if needed. No schema migration.

## Open Questions

- None blocking — name default and command name (`user:create`) can be finalized at implementation without further product input.
