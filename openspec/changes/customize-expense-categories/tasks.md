## 1. Category Data and Validation

- [x] 1.1 Add nullable color and icon columns and make them assignable on expense categories; verify existing categories still load with null appearance values and the category feature tests pass.
- [x] 1.2 Define a curated soft, muted allowlist of Flux badge colors and Flux Free icon names, then validate them when creating or updating a category; add Pest coverage for valid values, clearing values, and rejection of unsupported values.

## 2. Category and Expense Presentation

- [x] 2.1 Add color and icon selectors to category creation and editing, showing only soft, muted color choices and retaining unset values when no option is chosen; verify Livewire tests cover create, edit, and clear behavior.
- [x] 2.2 Render the selected color as the category badge background and the selected Flux icon in the shared expense row; verify Today and Monthly expense-row tests cover selected and unset appearance.

## 3. Verification

- [x] 3.1 Run `php artisan test --compact tests/Feature/ExpenseCategoryTest.php` and `vendor/bin/pint --dirty --format agent`; confirm the focused category suite passes and modified PHP files are formatted.