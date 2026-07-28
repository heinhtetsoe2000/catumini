## 1. Foundation (shared)

- [x] 1.1 Add `DisplayLanguage` and `Appearance` backed enums
- [x] 1.2 Migration: add nullable `display_language` and `appearance` columns on `users`
- [x] 1.3 Update `User` model fillable and casts for new columns
- [x] 1.4 Implement `SetOwnerDisplayPreferences` middleware (locale + appearance resolution, cookies, browser detect)
- [x] 1.5 Register middleware in `bootstrap/app.php` web stack
- [x] 1.6 Sync cookies from User on login; clear or preserve per design on logout

## 2. Display language — translations and assets

- [x] 2.1 Add `lang/en.json` and `lang/my.json` for app UI strings
- [x] 2.2 Add `lang/my/` copies for auth, validation, passwords, pagination
- [x] 2.3 Extract hardcoded English strings across Blade/Livewire views to `__()` keys
- [x] 2.4 Load Noto Sans Myanmar / Noto Serif Myanmar in layouts when locale is `my`; add CSS font overrides
- [x] 2.5 Implement `ExpenseDayLabel` (or equivalent) for localized Today/Yesterday/Gregorian day headers in Asia/Yangon
- [x] 2.6 Update `expense-record`, home, dashboard, and monthly views to use localized day labels

## 3. Display language — toggles and UI

- [x] 3.1 Build reusable **EN | MY** segmented toggle (Livewire or Blade component)
- [x] 3.2 Add language toggle to Login gate (`welcome.blade.php`) with instant apply + cookie write
- [x] 3.3 Add language toggle to Profile Livewire page with instant apply + User persist
- [x] 3.4 Persist browser-detected language to User on first authenticated request when column is null

## 4. Display language — tests

- [x] 4.1 Feature test: guest Login gate renders Burmese with locale cookie
- [x] 4.2 Feature test: browser Accept-Language detect sets cookie on first visit
- [x] 4.3 Feature test: authenticated Owner language toggle persists and reloads UI
- [x] 4.4 Feature test: Today/day group headers localize for `my` locale
- [x] 4.5 Feature test: validation error renders in Burmese when locale is `my`
- [x] 4.6 Run `php artisan test --compact` for affected tests; run Pint on dirty PHP

## 5. Owner appearance preference

- [x] 5.1 Wire resolved `Appearance` enum into Flux `@fluxAppearance` / layout (light, dark, system)
- [x] 5.2 Build **Light | Dark | System** segmented toggle component
- [x] 5.3 Add appearance toggle to Profile only with instant apply + User persist + cookie sync
- [x] 5.4 Treat null `appearance` as `system` in middleware

## 6. Appearance — tests

- [x] 6.1 Feature test: Profile appearance toggle persists `dark` and renders dark shell
- [x] 6.2 Feature test: `system` appearance follows OS preference (mock or document manual check)
- [x] 6.3 Feature test: guest appearance cookie applies on Login gate without toggle present
- [x] 6.4 Run `php artisan test --compact` for appearance tests; run Pint on dirty PHP
