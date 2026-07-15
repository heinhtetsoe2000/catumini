## 1. Navigation chrome

- [x] 1.1 Rebuild `layouts/navigation.blade.php`: always-visible Today | Monthly on all breakpoints; remove hamburger drawer and `bars-3` toggle
- [x] 1.2 Mobile (`< sm`): icon-only calendar-day / calendar-month peers with accessible names “Today” / “Monthly” (no house/chart icons)
- [x] 1.3 Desktop (`sm+`): keep text-labeled Today | Monthly peers
- [x] 1.4 Mobile account: compact person/avatar control opening Profile / Log Out menu; desktop keeps name + chevron
- [x] 1.5 Keep wordmark → `home`; `:current` only for exact `home` / `dashboard`; no Add Expense shell peer; no bottom nav

## 2. Verification

- [x] 2.1 Update `MobileWebShellTest` (and related assertions) for always-visible peers, no hamburger, account menu, no Add-as-peer, no `native:bottom-nav`
- [x] 2.2 Run `php artisan test --compact --filter=MobileWebShell` (and any other affected shell tests)
- [x] 2.3 Manually smoke Today ↔ Monthly one-tap on a narrow viewport (or document deferral); confirm Profile/Log Out via account control
  - Deferred to Owner: verify on iPhone Safari / narrow DevTools — nav is Blade/CSS only; automated coverage in 2.1–2.2
