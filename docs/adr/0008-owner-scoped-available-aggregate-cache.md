# Owner-scoped Available aggregate cache

**Income tracking** introduces **Available** — total received minus total spent, carry-over with no monthly reset — plus month-scoped received/spent/**month net** on the **Income** tab. Recomputing global sums from all Income and Expense rows on every strip and hero read would not scale; we cache Owner-scoped aggregates with the same lazy-fill and immediate invalidation pattern as ADR 0004.

**What we cache (Income tracking on):** per Owner global totals — total received, total spent, and derived **Available** (with the 0-floor rule applied in application code when the Owner has no Income rows yet). Per Owner current-month income total and month received map keyed by Yangon `Y-m`. Entries are filled lazy on read, invalidated on Income or Expense create/update/delete (both entity types bust global and month income keys; Expense writes also bust existing expense day/month keys via the existing service).

**0-floor rule:** when an Owner has zero Income rows, **Available** displayed everywhere is `0` regardless of Expense history; after the first Income row exists, **Available** is honest (including negative with alarm styling).

**Key shape (illustrative):** `owner:{id}:income:total`, `owner:{id}:expense:total`, `owner:{id}:month:{Y-m}:income` — exact helpers live in one shared reader; UI must not duplicate key strings.

**Rejected:** caching Income row lists; month-scoped **Available** that resets on the 1st (contradicts carry-over); recomputing **Available** only on the Income tab while strips show a different number; skipping cache and scanning all rows on every Livewire render.
