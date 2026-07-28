## Why

The ledger ships English-only UI with **Appearance** locked to OS system preference (ADR 0001), while the product targets an **Owner** on mobile web in Myanmar. ADR 0006 locks **Display language** (`en` / `my`) and **Appearance** (Light / Dark / System) as Owner-scoped preferences. **Display language** ships first so new UI is not translated twice; **Appearance** follows on the same storage pattern.

## What Changes

- Add **Display language** (`en` / `my`): Owner profile column, guest cookie bridge, browser detect on first visit, **EN | MY** segmented toggle on Profile and Login gate, instant apply with page reload for locale
- Add Burmese translations for in-app UI and Laravel validation/auth strings (`lang/my`); localized Gregorian day headers in Asia/Yangon; Noto Myanmar web fonts when `my`
- Leave **APP_NAME**, **Ks**, Owner-entered expense text, and system emails unchanged in MVP; Western digits with grouping for amounts
- Add **Appearance** (Light / Dark / System): same profile + cookie storage pattern; **Light | Dark | System** segmented toggle on Profile only; instant apply via Flux appearance
- Extract hardcoded English strings across Blade/Livewire views to translation keys
- Add middleware (or equivalent) to resolve locale and appearance from profile/cookie/browser on every request
- Add Pest coverage for locale resolution, toggle persistence, localized day headers, and appearance override

## Capabilities

### New Capabilities

- `display-language`: English and Burmese UI, validation/auth messages, fonts, localized dates, EN | MY toggle on Profile and Login gate, Owner + cookie persistence
- `owner-appearance-preference`: Light / Dark / System Owner preference with Profile toggle, cookie bridge for guests, Flux-driven appearance override extending ledger ink

### Modified Capabilities

- `ledger-ink-theme`: Appearance MUST support Owner override (Light / Dark / System), not only OS system preference; Burmese mode MUST use Myanmar-capable web fonts while English keeps ledger ink typefaces
- `login-gate`: Login gate copy MUST be translatable; **EN | MY** toggle MUST be available for guests; supporting line MUST use translation keys
- `expense-ledger`: Day group headers and relative labels (Today, Yesterday) MUST follow **Display language** with Gregorian calendar in Asia/Yangon
- `profile-logout`: Profile MUST expose **Display language** and **Appearance** segmented toggles with instant apply

## Impact

- Database: `users.display_language` and `users.appearance` (or equivalent enum columns)
- Models: `User` fillable/casts for new preference fields
- Middleware: locale and appearance resolution; cookie sync on login/logout and toggle actions
- Views: `welcome.blade.php`, guest layout, app layout, Profile Livewire, navigation, expense views — string extraction and toggles
- Assets: conditional Noto Myanmar font loading in layouts; `app.css` locale font classes if needed
- Lang: new `lang/my/` files (auth, validation, passwords, pagination, custom `lang/{locale}/*.json` or PHP for app strings)
- Config: supported locales list
- Tests: feature tests for guest/authenticated locale, appearance persistence, Burmese day headers
- Docs: ADR 0006 and CONTEXT.md glossary already updated
- Out of scope (MVP): Burmese system emails, Myanmar numerals, Buddhist calendar, appearance toggle on Login gate
