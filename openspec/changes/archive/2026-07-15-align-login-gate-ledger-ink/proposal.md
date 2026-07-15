## Why

The public `/` page is still a NativePHP starter splash (“Your app is ready”, framework links, Laravel logo), which contradicts the personal expense ledger **mobile-web MVP**. Guests and the Owner need a product-aligned **Login gate** and a shared visual system so login and the authenticated shell feel like one app—not a framework demo.

## What Changes

- Replace the NativePHP welcome splash with a **Login gate**: `APP_NAME`, tagline “Your personal expense ledger.”, and a Login link to `/login` (no register, no NativePHP promo, no logo mark)
- Redirect authenticated visitors from `/` to **Today** (`/home`)
- Introduce a shared **ledger ink** visual system (paper light + dark ink, serif titles / sans body, teal accent, wordmark-only) across the Login gate, guest auth layout/login, and authenticated shell
- Subtle entrance motion on the Login gate only
- Delete unrouted starter views `about.blade.php` and `settings.blade.php`
- Record visual decisions in `docs/adr/0001-ledger-ink-visual-system.md` (already drafted); keep domain language for **Login gate** in `CONTEXT.md`

## Capabilities

### New Capabilities

- `login-gate`: Public `/` entry behavior for guests and authenticated Owners; product-aligned copy and CTA; no marketing or framework chrome
- `ledger-ink-theme`: Shared visual language (tokens, typography, wordmark, accents, dual light/dark) for Login gate, guest layout, and authenticated shell

### Modified Capabilities

- `mobile-web-shell`: Authenticated shell branding and chrome must use ledger ink (wordmark instead of Laravel logo; theme tokens) while remaining a navigable mobile-web Today / Add / Monthly / Profile experience

## Impact

- Routes: `/` guest vs auth redirect
- Views: `welcome.blade.php` (Login gate), `layouts/guest.blade.php`, `layouts/app.blade.php`, `layouts/navigation.blade.php`, auth login (and guest-layout consumers), expense/shell pages using blue/gray Breeze accents
- CSS/Tailwind/Vite fonts: theme tokens, serif + sans, teal primary
- Cleanup: delete `resources/views/about.blade.php`, `resources/views/settings.blade.php`
- Docs: ADR + `CONTEXT.md` (Login gate already defined)
- Tests: Login gate content/behavior; authenticated `/` redirect; theme-related assertions as needed
