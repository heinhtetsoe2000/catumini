## Context

Personal Expense Ledger (Laravel 12, Flux Free, Livewire) ships English-only UI. Dark styles exist via `@fluxAppearance` and Tailwind `dark:` variants driven by OS system preference (ADR 0001). Some strings use `__()` but many Blade/Livewire views hardcode English. `User` has no preference columns. ADR 0006 locks **Display language** (`en` / `my`) and **Appearance** (Light / Dark / System) as Owner-scoped preferences with guest cookie bridging. **Display language** ships first; **Appearance** follows on the same storage pattern.

## Goals / Non-Goals

**Goals:**

- Persist **Display language** and **Appearance** on `users`; resolve on every request from profile (authenticated) or cookie + browser detect (guest)
- **EN | MY** toggle on Profile and Login gate; **Light | Dark | System** on Profile; instant apply
- Burmese: `lang/my` for validation/auth + app strings, Noto Myanmar fonts, localized Gregorian day headers in Asia/Yangon
- Pest coverage for resolution, persistence, toggles, and localized headers

**Non-Goals:**

- Burmese system emails, Myanmar numerals, Buddhist calendar
- Appearance toggle on Login gate
- Translating **APP_NAME**, **Ks**, or Owner-entered expense text
- Third locale beyond `en` and `my`

## Decisions

### 1. User columns and enums

- **Choice:** Add nullable `display_language` (`en`|`my`) and `appearance` (`light`|`dark`|`system`) on `users`; PHP backed enums `DisplayLanguage` and `Appearance` with string casting.
- **Rationale:** Explicit allowed values; easy validation on toggle endpoints.
- **Alternatives:** JSON preferences blob (rejected — overkill for two fields); session-only (rejected per ADR 0006).

### 2. Request resolution middleware

- **Choice:** Single `SetOwnerDisplayPreferences` middleware registered in `bootstrap/app.php` web stack:
  1. **Authenticated:** read `User.display_language` / `User.appearance`; if null, browser-detect language once and persist on first login path via listener or middleware side-effect; set `App::setLocale()` and appearance cookie/session for Flux.
  2. **Guest:** read cookies; if missing, `Accept-Language` for locale (prefer `my` if Burmese listed, else `en`); appearance cookie defaults to `system`.
  3. Set `html lang` attribute via shared layout; queue appearance for `@fluxAppearance`.
- **Rationale:** One place for precedence rules; keeps Livewire/views dumb.
- **Alternatives:** Separate locale/theme middleware (acceptable but duplicated cookie logic); Livewire-only locale (rejected — misses guest gate and validation).

### 3. Cookie bridge

- **Choice:** HTTP-only cookies `ledger_locale` and `ledger_appearance` (1 year, `SameSite=Lax`); sync from profile on login; write on toggle for guests and authenticated users; profile update on authenticated toggle.
- **Rationale:** Login gate and auth pages share guest layout without session auth.
- **Alternatives:** LocalStorage only (rejected — server must render first paint).

### 4. Toggle UX (Livewire)

- **Choice:** Reusable Livewire/Flux segmented toggle components:
  - `DisplayLanguageToggle` — `wire:click` sets locale, updates cookie + user column when authed, `$this->redirect(request()->header('Referer', '/'), navigate: false)` or full page reload for locale swap.
  - `AppearanceToggle` — sets appearance enum, updates cookie + user, dispatches Flux appearance client-side without full reload where possible.
- **Rationale:** Instant apply per ADR; language needs server re-render.
- **Alternatives:** Dedicated PATCH routes (works but more boilerplate for two toggles).

### 5. Translation file layout

- **Choice:** Mirror Laravel defaults under `lang/my/` (auth, validation, passwords, pagination). App UI strings in `lang/en.json` / `lang/my.json` (Laravel JSON translations) keyed by English source string or dot keys — follow existing `__()` usage in views.
- **Rationale:** JSON simplifies large UI sweep; PHP files for framework bundles.
- **Alternatives:** All PHP arrays (fine but verbose for UI sweep).

### 6. Localized dates

- **Choice:** Central helper (e.g. `ExpenseDayLabel::for(Carbon $date): string`) using `Carbon::locale(app()->getLocale())` for month names; `__('Today')`, `__('Yesterday')` for relative days; timezone `Asia/Yangon` unchanged.
- **Rationale:** Fixes hardcoded `'Today'` / `'Yesterday'` / `'D M d'` in `expense-record.blade.php` and home/monthly views.
- **Alternatives:** Inline Carbon in every view (rejected — duplication).

### 7. Burmese typography

- **Choice:** When `app()->getLocale() === 'my'`, layouts load Noto Sans Myanmar + Noto Serif Myanmar from Bunny Fonts; add `font-my` utility or `html[lang=my]` selector in `app.css` overriding `--font-sans` / `--font-serif`. English layouts unchanged.
- **Rationale:** ADR 0006; Source Sans/Serif lack Myanmar block.
- **Alternatives:** System font only (rejected — inconsistent on iPhone Safari).

### 8. Appearance via Flux

- **Choice:** Map `Appearance` enum to Flux appearance modes (`light`, `dark`, `system`); middleware or layout sets Flux-compatible class/script state from resolved preference; Profile toggle calls Flux client API after server persist.
- **Rationale:** `@fluxAppearance` already present in layouts.
- **Alternatives:** Manual `class="dark"` on `<html>` only (may fight Flux; prefer Flux-native path).

### 9. Phased delivery

- **Choice:** Tasks grouped Phase 1 (display language end-to-end) then Phase 2 (appearance). Shared migration may add both columns in Phase 1; appearance UI wired in Phase 2.
- **Rationale:** Proposal shipping order; shared columns avoid second migration.

## Risks / Trade-offs

- [Flash of wrong locale/theme on first paint] → Set cookies early; optional inline script in layout head for appearance before CSS (Flux pattern); accept brief reload on language change
- [Incomplete string audit] → Grep for hardcoded English in views; Pest smoke tests assert key Burmese strings on Today/Profile/Login gate
- [Burmese translation quality] → MVP uses developer Burmese; Owner can refine `lang/my.json` later
- [Livewire toast strings remain English] → Include Livewire PHP string extraction in Phase 1 audit
- [Carbon locale missing on server] → Ensure `my` locale available or ship minimal custom month names in helper fallback

## Migration Plan

1. Run migration adding nullable enum/string columns with defaults null (browser detect until set)
2. Deploy Phase 1 (language); existing Owners get browser detect on first request after login
3. Deploy Phase 2 (appearance); null appearance treated as `system` (current behavior)
4. Rollback: drop columns and remove middleware (cookies harmless)

## Open Questions

- Exact Flux appearance API for programmatic Light/Dark/System switch — confirm against installed Flux version at implement time
- Prefer single shared Blade component vs Livewire for Login gate language toggle (guest page may use simple form POST to avoid Livewire on welcome)
