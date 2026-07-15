## Context

Authenticated shell uses Flux Free + ledger ink (ADRs 0001–0002). Desktop already exposes **Today** | **Monthly** as top-nav peers with Profile in an account dropdown. Below `sm`, those peers are buried in a hamburger drawer — the pain ADR 0003 records. Domain language (Today / Monthly / Owner) stays as in `CONTEXT.md`; multi-user public launch is future, out of scope.

## Goals / Non-Goals

**Goals:**

- One-tap **Today** ↔ **Monthly** on mobile without opening a menu
- Icon-only mobile destinations (calendar metaphor); text labels on `sm+`
- Compact account control on mobile (avatar/person → Profile / Log Out); remove hamburger
- Preserve IA: wordmark → Today; Add Expense Today-only CTA; no bottom nav / sidebar
- Update `MobileWebShellTest` (and shell assertions) for the new chrome

**Non-Goals:**

- Bottom tab bar or NativePHP EDGE `native:bottom-nav`
- Dirty-form leave guards when navigating away from Add/Edit
- Promoting Add Expense into the shell
- Icon-only or icon+text on desktop
- Multi-user / open registration / tenancy
- Route, controller, or domain model changes

## Decisions

1. **Always-visible top primary destinations (not bottom nav)**  
   Keep Flux top-nav IA; un-hide (and restyle for mobile) Today | Monthly. Rejected bottom bar to stay aligned with prior “no bottom navigation” shell constraint while fixing tap tax.  
   *Rejected:* hamburger-only peers; sticky bottom tabs; swipe-only.

2. **Breakpoint presentation**  
   Mobile (`< sm`): icon-only peers with `aria-label` / accessible name “Today” / “Monthly”. Desktop (`sm+`): text-labeled `flux:navbar.item` peers as today.  
   *Rejected:* icon-only everywhere; icon+text on desktop; two-row destinations strip (icons reclaim one-row space).

3. **Calendar icon pair**  
   Use Flux/Heroicons in the calendar family (day vs month / multi-day). Never house or chart (Home / Dashboard analytics confusion per `CONTEXT.md`). Exact icon names chosen at implement time from Flux Free’s available set.  
   *Rejected:* house + chart; unlabeled icons without accessible names.

4. **Account control replaces hamburger**  
   Mobile: subtle person/avatar button opening the same Profile / Log Out menu as desktop. Remove `x-data` open drawer / `bars-3` toggle / mobile `flux:navlist` block. Desktop keeps name + chevron.  
   *Rejected:* hamburger with only Profile/Logout; Profile as a third always-visible peer.

5. **Current state = exact routes**  
   `:current` / equivalent only when `routeIs('home')` or `routeIs('dashboard')`. Add/Edit expense and Profile leave both peers unselected.  
   *Rejected:* highlighting Today while on Add/Edit.

6. **Wordmark and Add Expense unchanged**  
   Wordmark links to `home`. Add Expense remains sole CTA on Today (one `add-expense` href in primary chrome).  
   *Rejected:* brand-only wordmark; Add FAB / third shell peer.

7. **No leave guards**  
   Navigating from a dirty Add/Edit form discards fields (normal full-page navigation).  
   *Rejected:* beforeunload / confirm dialogs for this change.

8. **Docs**  
   ADR 0003 already records the decision; `CONTEXT.md` points at it. No new ADR during apply.

## Risks / Trade-offs

- **[Risk]** Icon-only destinations less discoverable for first-time Owner → **Mitigation:** Distinct calendar pair + correct `aria-label`s; desktop text remains; Owner already knows Today/Monthly names from prior use
- **[Risk]** Narrow phones still tight with wordmark + two icons + account → **Mitigation:** One row with icons; shorten/truncate wordmark styles only if visual QA fails — do not move destinations to a second row unless necessary
- **[Risk]** Tests assert hamburger / `Toggle navigation` / drawer markup → **Mitigation:** Update `MobileWebShellTest` for always-visible peer links + account menu affordance; assert absence of bottom-nav and Add-as-peer
- **[Trade-off]** Duplicate Today affordances (wordmark + icon) → Accepted; brand home remains intentional
- **[Trade-off]** Mid-form navigate loses data → Accepted for MVP; revisit at public multi-user phase if needed

## Migration Plan

- Edit `navigation.blade.php` only (plus tests); rebuild frontend assets if Flux/Vite cache needs it
- No DB or composer changes
- Rollback: revert nav + test commits

## Open Questions

- None blocking. Exact Heroicon names (`calendar` / `calendar-days` / equivalents) left to implementer against Flux Free’s icon set while matching ADR 0003 metaphors.
