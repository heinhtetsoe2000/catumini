# Owner-scoped Expense aggregate cache

Laravel Cloud cache is available; the ledger stays a single-**Owner** MVP but is aimed at a later public multi-user launch. We cache **Expense** aggregates only (not row lists), with keys namespaced by **Owner** and Asia/Yangon calendar dates, so Today/Monthly stay fast without painting us into an MVP-only corner.

**What we cache:** per-day total (integer) and per-month map of **Spend date** → day sum. Month total, average, and day−average difference are derived in PHP. Entries are filled lazy on read, invalidated immediately on **Expense** create/update/delete (via model events), and carry a long TTL only as a safety net. A shared aggregate service is the sole reader; Today (Livewire) and Monthly (`DashboardController`) both use it.

**Invalidation:** bust the affected day key(s) and month key(s) for that **Owner**. A **Spend date** change clears old and new day keys and both months when the move crosses a month boundary.

**Key shape:** `owner:{id}:day:{Y-m-d}` and `owner:{id}:month:{Y-m}` using Asia/Yangon calendar strings (aligned with **Today** / **Monthly** in `CONTEXT.md`).

**Rejected:** caching full Expense lists; short-TTL-only freshness; inline duplicate cache keys in each UI; UTC or “current”-relative keys; eager warming of many days/months.
