## Context

`/` currently returns `welcome.blade.php`, a NativePHP starter card with framework links and a Laravel logo fallback. Guest auth uses Breeze `x-guest-layout` (Figtree, gray cards, Laravel SVG). The authenticated shell uses the same Breeze stack with blue accents. Domain intent (personal expense ledger, closed registration, mobile-web MVP) is already in `CONTEXT.md` and ADR `0001-ledger-ink-visual-system.md`. This change implements that visual and entry-point agreement without changing expense-ledger domain behavior.

## Goals / Non-Goals

**Goals:**

- Replace `/` with a **Login gate** matching grilled product intent
- Redirect authenticated Owners from `/` to **Today**
- Apply one **ledger ink** theme across Login gate, guest layout (login + forgot/reset via inheritance), and authenticated shell
- Wordmark-only branding from `config('app.name')`; remove Laravel/NativePHP chrome from live surfaces
- Delete unrouted `about.blade.php` and `settings.blade.php`
- Test Login gate content/behavior and auth redirect

**Non-Goals:**

- NativePHP builds, EDGE chrome, or store listings
- Marketing landing, open registration, or inventing a logo asset
- Embedding the login form on `/`
- Redesigning expense CRUD flows beyond shared theme tokens/accents
- Motion on authenticated pages
- Changing APP_NAME values in deploy env (still env-driven)

## Decisions

1. **Route behavior for `/`**  
   Closure or thin controller: if `auth()->check()`, redirect to `route('home')`; else return Login gate view. Prefer naming the view `welcome` still (route/file renames optional) or rename to `login-gate` only if low-friction—implementation may keep `welcome.blade.php` as the gate view to minimize churn.

2. **Login remains at `/login`**  
   Gate CTA links to existing Breeze login. Forgot/reset stay on guest layout and inherit theme.

3. **Theme tokens via Tailwind/CSS**  
   Define paper ground, ink text, teal primary in `resources/css/app.css` (and Tailwind theme extension if needed). Load serif + sans from Bunny/fonts. Replace Figtree. Use `dark:` paired tokens for system preference. Swap primary buttons/links from `blue-*` / indigo Breeze defaults to teal accent utilities or CSS variables.

4. **Wordmark component**  
   Replace `<x-application-logo>` usages in guest/app nav with text wordmark (`config('app.name')`) using display/serif class. Keep or retire the logo Blade component if unused after cleanup.

5. **Login gate markup**  
   Prefer Blade + Vite CSS (shared app stylesheet) over inline NativePHP styles, so gate and app share tokens. Subtle CSS transitions/keyframes for wordmark, tagline, and Login CTA only.

6. **Cleanup**  
   Delete `resources/views/about.blade.php` and `resources/views/settings.blade.php` (unrouted).

7. **Docs**  
   ADR already present; keep `CONTEXT.md` Login gate language as source of truth for naming.

### Alternatives considered

- Auth-only theming → rejected (Q6 shared theme)  
- Embed login on `/` → rejected (separate gate + Breeze login)  
- Light-only theme → rejected (dual system)  
- Keep Figtree / Breeze blue → rejected (distinct ledger ink)

## Risks / Trade-offs

- **[Risk]** Broad Tailwind class churn misses some blue accents → **Mitigation:** Grep for `blue-`, `indigo-`, `application-logo`; add a smoke test that `/` and `/login` assert gate copy / absence of NativePHP strings  
- **[Risk]** Dark mode looking unfinished → **Mitigation:** Design deliberate dark-ink pairs for surfaces/text/accent, not only invert gray-900  
- **[Risk]** Font FOIT on mobile → **Mitigation:** `display=swap`, preconnect Bunny  
- **[Trade-off]** Keeping filename `welcome.blade.php` vs rename → Prefer content rename over route churn unless tests/docs already say “welcome”

## Migration Plan

- Ship as normal deploy; no DB migration  
- After deploy, Owners see new gate when logged out; logged-in visits to `/` land on Today  
- Rollback: revert views/CSS/route change  

## Open Questions

- None blocking; exact teal Tailwind shade (e.g. `teal-700`) and serif choice (e.g. Source Serif 4) left to implementer within ADR bounds
