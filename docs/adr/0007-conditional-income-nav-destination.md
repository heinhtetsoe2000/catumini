# Conditional Income nav destination

ADR 0003 locked shell primary peers to **Today** and **Monthly** only. Optional **Income tracking** adds a third primary destination — **Income** — between **Home** (Today) and **History** (Monthly) when the Owner enables the preference; it is fully absent when tracking is off so the expense-only ledger is unchanged.

On viewports below `sm`, **Income** is icon-only with an accessible name of **Income** (calendar or money-in metaphor — not house or chart icons). On `sm` and larger, it is a text-labeled top-nav peer. **Profile** stays in the account menu on desktop; mobile bottom bar may show four items when tracking is on (Home · Income · History · Profile). Current/selected state applies only on the Income route when visible.

We rejected folding income into **History** or **Home** (Owners wanted a dedicated wallet view), always showing the tab (contradicts optional feature), and renaming the tab **Balance** (implies a bank account; glossary uses **Income** for the nav destination and **Available** for the spendable amount).
