<?php

use App\Models\Expense;
use App\Models\User;
use App\Support\ExpenseCategoryAppearance;
use Illuminate\Database\QueryException;
use Livewire\Livewire;

test('category names are trimmed and normalized for owner-scoped uniqueness', function () {
    $owner = User::factory()->create();

    $category = $owner->expenseCategories()->create(['name' => '  Groceries  ']);

    expect($category->fresh())
        ->name->toBe('Groceries')
        ->name_key->toBe('groceries')
        ->user_id->toBe($owner->id);

    expect(fn () => $owner->expenseCategories()->create(['name' => 'gROCERIES']))
        ->toThrow(QueryException::class);
});

test('different owners can use the same category name', function () {
    $firstOwner = User::factory()->create();
    $secondOwner = User::factory()->create();

    $firstCategory = $firstOwner->expenseCategories()->create(['name' => 'Food']);
    $secondCategory = $secondOwner->expenseCategories()->create(['name' => 'Food']);

    expect($firstCategory->user_id)->toBe($firstOwner->id)
        ->and($secondCategory->user_id)->toBe($secondOwner->id);
});

test('category appearance is optional and persisted', function () {
    $owner = User::factory()->create();
    $existingCategory = $owner->expenseCategories()->create(['name' => 'Food']);
    $styledCategory = $owner->expenseCategories()->create([
        'name' => 'Travel',
        'color' => 'sky',
        'icon' => 'map-pin',
    ]);

    expect($existingCategory->fresh())
        ->color->toBeNull()
        ->icon->toBeNull()
        ->and($styledCategory->fresh())
        ->color->toBe('sky')
        ->icon->toBe('map-pin');
});

test('category manager validates appearance choices when creating and updating', function () {
    $owner = User::factory()->create();

    Livewire::actingAs($owner)->test('pages::categories')
        ->set('name', 'Travel')
        ->set('color', 'sky')
        ->set('icon', 'map-pin')
        ->call('createCategory')
        ->assertHasNoErrors();

    $category = $owner->expenseCategories()->where('name', 'Travel')->firstOrFail();
    expect(ExpenseCategoryAppearance::COLORS)->toContain($category->color)
        ->and(ExpenseCategoryAppearance::ICONS)->toContain($category->icon);

    Livewire::actingAs($owner)->test('pages::categories')
        ->set('name', 'Invalid')
        ->set('color', 'red')
        ->set('icon', 'not-an-icon')
        ->call('createCategory')
        ->assertHasErrors(['color', 'icon'])
        ->call('openRenameModal', $category->id)
        ->set('editingColor', 'red')
        ->set('editingIcon', 'not-an-icon')
        ->call('renameCategory')
        ->assertHasErrors(['editingColor', 'editingIcon']);

    expect($category->fresh())
        ->color->toBe('sky')
        ->icon->toBe('map-pin');
});

test('owner can create edit and clear category appearance in the category manager', function () {
    $owner = User::factory()->create();

    Livewire::actingAs($owner)->test('pages::categories')
        ->assertSee('Category color')
        ->assertSee('Category icon')
        ->assertSeeHtml('data-flux-dropdown')
        ->assertSeeHtml('aria-label="Shopping Cart"')
        ->assertSeeHtml('class="sr-only"')
        ->set('name', 'Travel')
        ->set('color', 'sky')
        ->set('icon', 'map-pin')
        ->call('createCategory')
        ->assertHasNoErrors();

    $category = $owner->expenseCategories()->where('name', 'Travel')->firstOrFail();

    $manager = Livewire::actingAs($owner)->test('pages::categories')
        ->call('openRenameModal', $category->id)
        ->assertSet('editingColor', 'sky')
        ->assertSet('editingIcon', 'map-pin')
        ->set('editingColor', 'pink')
        ->set('editingIcon', 'heart')
        ->call('renameCategory')
        ->assertHasNoErrors();

    expect($category->fresh())
        ->color->toBe('pink')
        ->icon->toBe('heart');

    $manager->call('openRenameModal', $category->id)
        ->set('editingColor', null)
        ->set('editingIcon', null)
        ->call('renameCategory')
        ->assertHasNoErrors();

    expect($category->fresh())
        ->color->toBeNull()
        ->icon->toBeNull();
});

test('expenses may be uncategorized and survive category deletion', function () {
    $user = User::factory()->create();
    $uncategorizedExpense = Expense::factory()->create(['user_id' => $user->id]);
    $category = $user->expenseCategories()->create(['name' => 'Food']);
    $categorizedExpense = Expense::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'name' => 'Lunch',
        'amount' => 4500,
    ]);

    expect($uncategorizedExpense->category_id)->toBeNull()
        ->and($categorizedExpense->category->id)->toBe($category->id);

    $category->delete();

    $categorizedExpense->refresh();

    $this->assertModelExists($categorizedExpense);
    expect($categorizedExpense)
        ->category_id->toBeNull()
        ->name->toBe('Lunch')
        ->amount->toBe(4500);
});

test('expense creation and editing reject another owners category', function () {
    $owner = User::factory()->create();
    $otherOwner = User::factory()->create();
    $foreignCategory = $otherOwner->expenseCategories()->create(['name' => 'Private']);
    $expense = Expense::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)
        ->test('pages::home')
        ->set('name', 'Lunch')
        ->set('amount', 4500)
        ->set('spent_on', now()->toDateString())
        ->set('category_id', $foreignCategory->id)
        ->call('save')
        ->assertHasErrors(['category_id']);

    Livewire::actingAs($owner)
        ->test('expense.edit', ['expense' => $expense])
        ->set('category_id', $foreignCategory->id)
        ->call('update')
        ->assertHasErrors(['category_id']);

    expect($expense->fresh()->category_id)->toBeNull();
    $this->assertDatabaseMissing('expenses', ['name' => 'Lunch', 'user_id' => $owner->id]);
});

test('owner can manage categories without seeing another owners categories', function () {
    $owner = User::factory()->create();
    $otherOwner = User::factory()->create();
    $otherOwner->expenseCategories()->create(['name' => 'Private']);

    $manager = Livewire::actingAs($owner)->test('pages::categories')
        ->assertSee('No categories yet')
        ->assertDontSee('Private')
        ->set('name', '  Groceries  ')
        ->set('color', 'sky')
        ->set('icon', 'shopping-cart')
        ->call('createCategory')
        ->assertHasNoErrors()
        ->assertSee('Groceries')
        ->assertSeeHtml('flex flex-wrap items-center gap-2')
        ->assertSeeHtml('data-category-edit-trigger')
        ->assertSeeHtml('cursor-pointer')
        ->assertSeeHtml('data-category-preview')
        ->assertSeeHtml('text-base')
        ->assertSeeHtml('bg-sky-400/20')
        ->assertSeeHtml('data-flux-badge-icon')
        ->assertDontSee('Private');

    $category = $owner->expenseCategories()->where('name', 'Groceries')->firstOrFail();

    $manager->call('openRenameModal', $category->id)
        ->assertSet('editingName', 'Groceries')
        ->set('editingName', 'Food')
        ->call('renameCategory')
        ->assertHasNoErrors()
        ->assertSee('Food');

    expect($category->fresh()->name)->toBe('Food');
});

test('category names are required and unique when creating and renaming', function () {
    $owner = User::factory()->create();
    $owner->expenseCategories()->create(['name' => 'Food']);
    $secondCategory = $owner->expenseCategories()->create(['name' => 'Travel']);

    Livewire::actingAs($owner)->test('pages::categories')
        ->set('name', '')
        ->call('createCategory')
        ->assertHasErrors(['name' => 'required']);

    Livewire::actingAs($owner)->test('pages::categories')
        ->set('name', ' food ')
        ->call('createCategory')
        ->assertHasErrors(['name']);

    Livewire::actingAs($owner)->test('pages::categories')
        ->call('openRenameModal', $secondCategory->id)
        ->set('editingName', 'FOOD')
        ->call('renameCategory')
        ->assertHasErrors(['editingName']);

    expect($secondCategory->fresh()->name)->toBe('Travel');
});

test('category manager route is authenticated and linked in navigation', function () {
    $this->get(route('categories'))->assertRedirect(route('login'));

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('categories'));

    $response->assertSuccessful();
    $response->assertSee(__('Categories'));
    $response->assertSee(route('categories'));
});

test('owner can confirm deletion of a category and preserve assigned expenses', function () {
    $owner = User::factory()->create();
    $category = $owner->expenseCategories()->create(['name' => 'Food']);
    $expense = Expense::factory()->create([
        'user_id' => $owner->id,
        'category_id' => $category->id,
        'name' => 'Lunch',
    ]);

    Livewire::actingAs($owner)->test('pages::categories')
        ->call('openRenameModal', $category->id)
        ->assertSeeHtml('wire:click="openDeleteModal(\''.$category->id.'\')"')
        ->call('openDeleteModal', $category->id)
        ->assertSet('deletingCategoryName', 'Food')
        ->assertSee('Expenses assigned to this category will become uncategorized.')
        ->call('deleteCategory');

    $this->assertModelMissing($category);
    expect($expense->fresh())
        ->category_id->toBeNull()
        ->name->toBe('Lunch');
});

test('category manager cannot open another owners category', function () {
    $owner = User::factory()->create();
    $otherOwner = User::factory()->create();
    $foreignCategory = $otherOwner->expenseCategories()->create(['name' => 'Private']);

    Livewire::actingAs($owner)->test('pages::categories')
        ->call('openRenameModal', $foreignCategory->id)
        ->assertForbidden();
});

test('owner can assign change and clear an expense category', function () {
    $owner = User::factory()->create();
    $otherOwner = User::factory()->create();
    $food = $owner->expenseCategories()->create([
        'name' => 'Food',
        'color' => 'sky',
        'icon' => 'shopping-cart',
    ]);
    $travel = $owner->expenseCategories()->create([
        'name' => 'Travel',
        'color' => 'rose',
        'icon' => 'wallet',
    ]);
    $otherOwner->expenseCategories()->create(['name' => 'Private']);

    Livewire::actingAs($owner)->test('pages::home')
        ->assertSee('Food')
        ->assertSee('Travel')
        ->assertDontSee('Private')
        ->assertSeeHtml('w-28 shrink-0')
        ->assertSeeHtml('max-h-56')
        ->assertSeeHtml('data-expense-category-selected')
        ->assertSee(__('No category'))
        ->assertSeeHtml('data-expense-category-option')
        ->assertSeeHtml('bg-sky-400/20')
        ->assertSeeHtml('bg-rose-400/20')
        ->assertSeeHtml('data-flux-badge-icon')
        ->set('name', 'Lunch')
        ->set('amount', 4500)
        ->set('spent_on', now()->toDateString())
        ->set('category_id', $food->id)
        ->call('save')
        ->assertHasNoErrors();

    $expense = Expense::query()->where('name', 'Lunch')->firstOrFail();
    expect($expense->category_id)->toBe($food->id);

    $uncategorizedExpense = Expense::factory()->create(['user_id' => $owner->id]);

    Livewire::actingAs($owner)->test('expense.edit', ['expense' => $uncategorizedExpense])
        ->assertSeeHtml('data-expense-category-selected')
        ->assertSee(__('No category'));

    Livewire::actingAs($owner)->test('expense.edit', ['expense' => $expense])
        ->assertSeeHtml('max-h-56')
        ->assertSeeHtml('w-28 shrink-0')
        ->assertSeeHtml('data-expense-category-selected')
        ->assertSeeHtml('data-expense-category-option')
        ->assertSeeHtml('bg-sky-400/20')
        ->assertSeeHtml('bg-rose-400/20')
        ->assertSeeHtml('data-flux-badge-icon')
        ->set('category_id', $travel->id)
        ->call('update')
        ->assertHasNoErrors();

    expect($expense->fresh()->category_id)->toBe($travel->id);

    Livewire::actingAs($owner)->test('expense.edit', ['expense' => $expense->fresh()])
        ->set('category_id', null)
        ->call('update')
        ->assertHasNoErrors();

    expect($expense->fresh()->category_id)->toBeNull();
});

test('owner can create an expense without selecting a category', function () {
    $owner = User::factory()->create();

    Livewire::actingAs($owner)->test('pages::home')
        ->set('name', 'Coffee')
        ->set('amount', 2500)
        ->set('spent_on', now()->toDateString())
        ->call('save')
        ->assertHasNoErrors();

    expect(Expense::query()->where('name', 'Coffee')->firstOrFail()->category_id)->toBeNull();
});

test('today and monthly expense rows display assigned category names', function () {
    $owner = User::factory()->create();
    $category = $owner->expenseCategories()->create([
        'name' => 'Groceries',
        'color' => 'sky',
        'icon' => 'shopping-cart',
    ]);
    $categoryWithoutAppearance = $owner->expenseCategories()->create(['name' => 'Utilities']);

    Expense::factory()->create([
        'user_id' => $owner->id,
        'category_id' => $category->id,
        'name' => 'Lunch',
        'spent_on' => now()->toDateString(),
    ]);
    Expense::factory()->create([
        'user_id' => $owner->id,
        'category_id' => $categoryWithoutAppearance->id,
        'name' => 'Coffee',
        'spent_on' => now()->toDateString(),
    ]);

    Livewire::actingAs($owner)->test('pages::home')
        ->assertSee('Lunch')
        ->assertSee('Coffee')
        ->assertSeeHtml('data-expense-category')
        ->assertSee('Groceries')
        ->assertSee('Utilities')
        ->assertSeeHtml('bg-sky-400/20')
        ->assertSeeHtml('bg-zinc-400/15')
        ->assertSeeHtml('data-flux-badge-icon')
        ->assertSeeHtml('rounded-full')
        ->assertSeeHtml('flex min-w-0 flex-col items-start')
        ->assertSeeHtml('flex shrink-0 flex-col items-end gap-1');

    Livewire::actingAs($owner)->test('pages::dashboard')
        ->assertSee('Lunch')
        ->assertSee('Coffee')
        ->assertSeeHtml('data-expense-category')
        ->assertSee('Groceries')
        ->assertSee('Utilities')
        ->assertSeeHtml('bg-sky-400/20')
        ->assertSeeHtml('bg-zinc-400/15')
        ->assertSeeHtml('data-flux-badge-icon')
        ->assertSeeHtml('rounded-full')
        ->assertSeeHtml('flex min-w-0 flex-col items-start')
        ->assertSeeHtml('flex shrink-0 flex-col items-end gap-1');
});

test('today and monthly totals include categorized and uncategorized expenses', function () {
    $owner = User::factory()->create();
    $category = $owner->expenseCategories()->create(['name' => 'Food']);

    Expense::factory()->create([
        'user_id' => $owner->id,
        'category_id' => $category->id,
        'amount' => 1800,
        'spent_on' => now()->toDateString(),
    ]);
    Expense::factory()->create([
        'user_id' => $owner->id,
        'amount' => 2200,
        'spent_on' => now()->toDateString(),
    ]);

    Livewire::actingAs($owner)->test('pages::home')
        ->assertSet('total', 4000)
        ->assertSee('4,000 Ks');

    Livewire::actingAs($owner)->test('pages::dashboard')
        ->assertSet('total', 4000)
        ->assertSee('4,000 Ks');
});
