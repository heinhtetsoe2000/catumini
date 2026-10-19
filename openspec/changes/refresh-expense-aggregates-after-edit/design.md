## Context

See `proposal.md` for the reported behavior. The application uses a shared `ExpenseAggregateCache` for per-day, per-month, and global spend totals. `ExpenseObserver` invalidates those keys on updates, but the nested Expense editor did not notify its parent page after saving. As a result, Home's persisted computed values and Dashboard's aggregate state could remain stale for the current Livewire page instance even though the shared cache was fresh.

## Goals / Non-Goals

**Goals:**

- Notify the parent page after a successful Expense edit so it refreshes persisted computed values or rebuilds monthly aggregate state.
- Keep cache invalidation centralized and ensure all affected spend and balance aggregates are fresh after the write.
- Add regression tests that warm caches before editing and verify refreshed values in service and user-facing reads.

**Non-Goals:**

- Change cache key formats, TTL policy, or the aggregate formulas.
- Cache Expense lists or introduce new dependencies.
- Change Income behavior or the visible layout.

## Decisions

- Dispatch an Expense-specific update event after the child editor successfully saves, then handle it in both Home and Dashboard.
- On Home, clear persisted computed values through the existing refresh method. On Dashboard, recalculate monthly spend, income, and available values and reload the grouped Expense list.
- Preserve the existing shared invalidation service as the owner of day, month, total-spent, and derived **Available** cache freshness; do not add view-specific cache busting.
- Test with the relevant cache keys warmed before the edit. Verify daily/monthly totals and global total spent/**Available** after the write, including the user-facing flow, so a test cannot pass by only checking that a key was forgotten.
- Keep owner scoping and date-change handling intact; when the spend date also changes, both old and new date/month aggregates must remain correct.

## Risks / Trade-offs

- [Service-level invalidation tests pass while the actual edit path still bypasses events] → Exercise the same update route/component used by the user and assert refreshed values, not only cache key state.
- [A fix invalidates only one aggregate family] → Cover day, month, global spend, and **Available** in the regression cases.
- [Tests depend on income tracking being enabled to display **Available**] → Use the existing income-tracking factory state and assert the expected rendered aggregate only in that mode.

## Migration Plan

No schema or data migration is expected. Deploy the application change and tests together. Rollback is a normal code rollback; existing stale cache entries expire according to the current TTL.