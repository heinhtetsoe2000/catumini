## Context

See proposal.md for motivation. Expense categories are owner-scoped Eloquent records. The Livewire categories page creates, renames, and deletes them, while the shared expense row renders the category as a Flux badge. The category table currently stores only its name and normalized name key.

## Goals / Non-Goals

**Goals:**
- Keep category appearance optional and preserve existing categories without a data backfill.
- Store only values from the supported Flux color and Flux Free icon sets.
- Reuse the existing category management and expense-row components.

**Non-Goals:**
- Arbitrary CSS colors, uploaded icons, or user-provided SVGs.
- Appearance customization for income or other entities.
- Changing category ownership, assignment, or deletion behavior.

## Decisions

- Store nullable `color` and `icon` identifiers on each category. Additive nullable columns allow current records to keep their existing appearance without a rewrite or required default.
- Use a curated finite allowlist of soft, muted color values supported by Flux badges, excluding saturated or dark options, and a finite allowlist of icon names supported by Flux Free. Validate values on create and update, and render them through Flux components rather than interpolating arbitrary CSS or markup. This avoids unsafe styling input and keeps persisted values compatible with the installed UI kit.
- Extend the existing category create and edit flows with color and icon selectors. Leave either value unset when the owner chooses no option; existing categories then retain their current zinc badge appearance and no icon.
- Render a selected icon inside the existing category badge and pass a selected color to the badge's color option. Because expense rows are shared by Today and Monthly views, one presentation change covers both.
- Keep the change within the current Laravel, Livewire, and Flux dependencies; no package is needed.

## Risks / Trade-offs

- [Risk] A persisted color or icon becomes unsupported after a UI-kit update → Mitigation: centralize the allowlists and preserve a fallback to the existing badge appearance when rendering legacy or unsupported values.
- [Risk] Some color choices may reduce label contrast in light or dark appearance → Mitigation: offer only Flux badge colors with built-in foreground treatment and verify both appearances.

## Migration Plan

Add nullable color and icon columns to the existing expense categories table. Deploy the migration before the application code that reads the new fields. Rollback drops only those columns; category names and expense assignments remain unchanged.