## 1. Theme foundations

- [x] 1.1 Add ledger ink CSS tokens (paper/ink surfaces, text, teal accent) with `dark:` / prefers-color-scheme pairs in `resources/css/app.css` (and Tailwind theme if needed)
- [x] 1.2 Load serif + sans fonts (replace Figtree) in `layouts/app.blade.php` and `layouts/guest.blade.php`
- [x] 1.3 Create a text wordmark Blade component (or equivalent) using `config('app.name')`; replace `<x-application-logo>` in guest layout and navigation

## 2. Login gate & routing

- [x] 2.1 Update `/` route: authenticated users redirect to `home` (Today); guests render the Login gate view
- [x] 2.2 Rewrite `welcome.blade.php` (or renamed gate view) with wordmark, “Your personal expense ledger.”, Login link to `/login`, Vite styles, subtle entrance motion only; remove NativePHP logo, links, footer, and purple badge chrome
- [x] 2.3 Delete unrouted `resources/views/about.blade.php` and `resources/views/settings.blade.php`

## 3. Guest auth + authenticated shell theming

- [x] 3.1 Restyle `layouts/guest.blade.php` for ledger ink (wordmark, paper/dark surfaces) so login/forgot/reset inherit it
- [x] 3.2 Restyle `layouts/app.blade.php` and `layouts/navigation.blade.php` for ledger ink; ensure nav brand is wordmark linking to Today
- [x] 3.3 Replace leftover Breeze `blue-*` / indigo primary accents on expense/shell pages and buttons with teal accent tokens
- [x] 3.4 Confirm Profile and auth pages remain usable under the new theme (no NativePHP EDGE chrome)

## 4. Tests & verify

- [x] 4.1 Update/extend `HomePageTest` (or new LoginGate test): guest `/` asserts app name, tagline, login link; asserts absence of NativePHP promo links and registration CTA
- [x] 4.2 Feature test: authenticated user requesting `/` redirects to Today (`home`)
- [x] 4.3 Feature/view assertions: login and Today use wordmark (no Laravel logo SVG as brand); Today still has no EDGE native components
- [x] 4.4 Run affected Pest tests (`php artisan test --compact` for touched tests) and Pint on dirty PHP
