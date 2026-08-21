---
paths:
  - 'app/Models/**'
---

# Models

## Legacy model accessors and mutators
Use the legacy magic-method style for model accessors and mutators (`getXxxAttribute()` / `setXxxAttribute()`), not the `Attribute` class.

## Model query scopes for app-specific filters
Keep filtering logic in model-local query scopes (`scopeCurrentUser()`, `scopeToday()`, etc.) instead of building separate repository/query classes for common model queries.
