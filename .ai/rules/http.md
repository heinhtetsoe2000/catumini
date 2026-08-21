---
paths:
  - 'app/Http/**'
---

# Http

## Inline validation in controllers
Validate request data inline with `$request->validate([...])` in controller methods and Livewire components; prefer that over Form Request classes for request validation.
