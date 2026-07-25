# Personal Expense Ledger

[![Laravel](https://github.com/heinhtetsoe2000/catumini/actions/workflows/laravel.yml/badge.svg)](https://github.com/heinhtetsoe2000/catumini/actions/workflows/laravel.yml)

A personal money-out journal for a single Owner. Ship as a mobile-web app (iPhone Safari); amounts are whole-unit **Ks**, and **Today** / **Monthly** are driven by spend date in `Asia/Yangon`.

## What it does

- Create, edit, and delete personal expenses (integer amount, optional description, spend date)
- **Today** at `/home` and **Monthly** at `/dashboard`
- Closed registration — login and password reset only; no public sign-up
- Login gate at `/` for guests; authenticated users are sent to Today
- Application timezone: `Asia/Yangon`

## Stack

- Laravel 12, Livewire Flux, Laravel Breeze (auth)
- Tailwind CSS + Vite
- Pest
- SQLite by default

NativePHP Mobile remains a Composer dependency from the starter, but is not required for the current web MVP.

## Requirements

- PHP 8.4+
- Composer
- Node.js and npm

## Local setup

```bash
composer setup
php artisan db:seed
```

`composer setup` installs PHP/JS dependencies, copies `.env` if needed, generates the app key, runs migrations, and builds assets.

Owner login comes from `.env` (see `.env.example`):

- `OWNER_NAME`
- `OWNER_EMAIL`
- `OWNER_PASSWORD`

Then run the local stack:

```bash
composer run dev
```

## Tests

```bash
composer test
```

Or: `php artisan test`

CI runs Pest on push and pull requests to `main` / `develop` (see `.github/workflows/laravel.yml`).

## Docs

- [CONTEXT.md](CONTEXT.md) — domain language and product scope
- [docs/adr/](docs/adr/) — architecture and UI decisions
