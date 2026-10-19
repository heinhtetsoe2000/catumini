<?php

use App\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $name = '';

    public string $editingName = '';

    public ?string $editingCategoryId = null;

    public ?string $deletingCategoryId = null;

    public string $deletingCategoryName = '';

    #[Computed]
    public function categories(): Collection
    {
        return Auth::user()->expenseCategories()->orderBy('name')->get();
    }

    public function createCategory(): void
    {
        $validated = $this->validate([
            'name' => $this->categoryNameRules(),
        ]);

        Auth::user()->expenseCategories()->create([
            'name' => trim($validated['name']),
        ]);

        $this->reset('name');
        unset($this->categories);
    }

    public function openRenameModal(string $categoryId): void
    {
        $category = $this->findOwnedCategory($categoryId);

        $this->editingCategoryId = $category->id;
        $this->editingName = $category->name;
        $this->modal('edit-category')->show();
    }

    public function renameCategory(): void
    {
        abort_if($this->editingCategoryId === null, 404);

        $category = $this->findOwnedCategory($this->editingCategoryId);
        $validated = $this->validate([
            'editingName' => $this->categoryNameRules($category->id),
        ]);

        $category->update([
            'name' => trim($validated['editingName']),
        ]);

        $this->modal('edit-category')->close();
        $this->reset('editingCategoryId', 'editingName');
        unset($this->categories);
    }

    public function openDeleteModal(string $categoryId): void
    {
        $category = $this->findOwnedCategory($categoryId);

        $this->deletingCategoryId = $category->id;
        $this->deletingCategoryName = $category->name;
        $this->modal('delete-category')->show();
    }

    public function deleteCategory(): void
    {
        abort_if($this->deletingCategoryId === null, 404);

        $category = $this->findOwnedCategory($this->deletingCategoryId);
        $category->delete();

        $this->modal('delete-category')->close();
        $this->reset('deletingCategoryId', 'deletingCategoryName');
        unset($this->categories);
    }

    private function categoryNameRules(?string $exceptCategoryId = null): array
    {
        return [
            'required',
            'string',
            'max:255',
            function (string $attribute, mixed $value, \Closure $fail) use ($exceptCategoryId): void {
                $query = Auth::user()->expenseCategories()
                    ->where('name_key', ExpenseCategory::normalizeName((string) $value));

                if ($exceptCategoryId !== null) {
                    $query->where('id', '!=', $exceptCategoryId);
                }

                if ($query->exists()) {
                    $fail(__('A category with this name already exists.'));
                }
            },
        ];
    }

    private function findOwnedCategory(string $categoryId): ExpenseCategory
    {
        $category = Auth::user()->expenseCategories()->find($categoryId);
        abort_unless($category instanceof ExpenseCategory, 403);

        return $category;
    }
};
?>

<div class="mx-auto m-4 w-90 md:w-auto max-w-2xl">
    <flux:card class="px-4 sm:px-6 lg:px-8">
        <flux:heading size="lg" class="font-bold capitalize text-2xl dark:text-ink-invert">
            {{ __('Categories') }}
        </flux:heading>
        <flux:text class="mt-2">{{ __('Manage your expense categories.') }}</flux:text>

        <form wire:submit="createCategory" class="mt-4 flex flex-col items-stretch gap-2 sm:flex-row sm:items-end">
            <flux:field class="min-w-0 flex-1">
                <flux:label>{{ __('Category name') }}</flux:label>
                <flux:input name="name" wire:model="name" required />
                <flux:error name="name" />
            </flux:field>
            <flux:button class="w-full sm:w-auto" type="submit" icon="plus" variant="primary" color="zinc">
                {{ __('Add Category') }}
            </flux:button>
        </form>

        <flux:separator class="my-4" />

        @forelse ($this->categories as $category)
            <div wire:key="category-{{ $category->id }}" class="flex items-center justify-between gap-3 py-3">
                <flux:text class="min-w-0 truncate font-medium">{{ $category->name }}</flux:text>
                <div class="flex shrink-0 items-center gap-1">
                    <flux:tooltip :content="__('Rename Category')">
                        <flux:button type="button" icon="pencil" variant="ghost" :aria-label="__('Rename Category')" wire:click="openRenameModal('{{ $category->id }}')" />
                    </flux:tooltip>
                    <flux:tooltip :content="__('Delete Category')">
                        <flux:button type="button" icon="trash" variant="ghost" :aria-label="__('Delete Category')" wire:click="openDeleteModal('{{ $category->id }}')" />
                    </flux:tooltip>
                </div>
            </div>
            @if (!$loop->last)
                <flux:separator />
            @endif
        @empty
            <flux:text class="py-4 text-center">{{ __('No categories yet') }}</flux:text>
        @endforelse
    </flux:card>

    <flux:modal name="edit-category" class="w-90 md:w-auto">
        <div class="space-y-6">
            <flux:heading size="lg">{{ __('Rename Category') }}</flux:heading>

            <form wire:submit="renameCategory" class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('Category name') }}</flux:label>
                    <flux:input name="editingName" wire:model="editingName" required />
                    <flux:error name="editingName" />
                </flux:field>

                <div class="flex justify-between gap-2">
                    <flux:button class="w-full" variant="outline" color="zinc" x-on:click="$flux.modal('edit-category').close()">{{ __('Cancel') }}</flux:button>
                    <flux:button class="w-full" variant="primary" color="zinc" type="submit">{{ __('Save') }}</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <flux:modal name="delete-category" class="w-90 md:w-auto">
        <div class="space-y-6">
            <flux:heading size="lg">{{ __('Delete Category') }}</flux:heading>
            <flux:text class="font-medium">{{ $deletingCategoryName }}</flux:text>
            <flux:text>{{ __('Expenses assigned to this category will become uncategorized.') }}</flux:text>

            <div class="flex justify-between gap-2">
                <flux:button class="w-full" variant="outline" color="zinc" x-on:click="$flux.modal('delete-category').close()">{{ __('Cancel') }}</flux:button>
                <flux:button class="w-full" variant="danger" wire:click="deleteCategory">{{ __('Delete') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>