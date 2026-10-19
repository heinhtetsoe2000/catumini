<?php

use App\Models\ExpenseCategory;
use App\Support\ExpenseCategoryAppearance;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $name = '';

    public ?string $color = null;

    public ?string $icon = null;

    public string $editingName = '';

    public ?string $editingColor = null;

    public ?string $editingIcon = null;

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
            'color' => ['nullable', 'string', Rule::in(ExpenseCategoryAppearance::COLORS)],
            'icon' => ['nullable', 'string', Rule::in(ExpenseCategoryAppearance::ICONS)],
        ]);

        Auth::user()->expenseCategories()->create([
            'name' => trim($validated['name']),
            'color' => $validated['color'],
            'icon' => $validated['icon'],
        ]);

        $this->reset('name', 'color', 'icon');
        unset($this->categories);
    }

    public function openRenameModal(string $categoryId): void
    {
        $category = $this->findOwnedCategory($categoryId);

        $this->editingCategoryId = $category->id;
        $this->editingName = $category->name;
        $this->editingColor = $category->color;
        $this->editingIcon = $category->icon;
        $this->modal('edit-category')->show();
    }

    public function renameCategory(): void
    {
        abort_if($this->editingCategoryId === null, 404);

        $category = $this->findOwnedCategory($this->editingCategoryId);
        $validated = $this->validate([
            'editingName' => $this->categoryNameRules($category->id),
            'editingColor' => ['nullable', 'string', Rule::in(ExpenseCategoryAppearance::COLORS)],
            'editingIcon' => ['nullable', 'string', Rule::in(ExpenseCategoryAppearance::ICONS)],
        ]);

        $category->update([
            'name' => trim($validated['editingName']),
            'color' => $validated['editingColor'],
            'icon' => $validated['editingIcon'],
        ]);

        $this->modal('edit-category')->close();
        $this->reset('editingCategoryId', 'editingName', 'editingColor', 'editingIcon');
        unset($this->categories);
    }

    public function openDeleteModal(string $categoryId): void
    {
        $category = $this->findOwnedCategory($categoryId);

        $this->deletingCategoryId = $category->id;
        $this->deletingCategoryName = $category->name;
        $this->modal('edit-category')->close();
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

        <form wire:submit="createCategory" class="mt-4 space-y-4">
            <div class="grid grid-cols-[minmax(0,1fr)_auto_auto] items-end gap-2">
                <flux:field class="min-w-0">
                    <flux:label class="sr-only">{{ __('Category name') }}</flux:label>
                    <flux:input name="name" wire:model="name" required />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:dropdown position="bottom" align="start">
                        <flux:button type="button" variant="outline" class="!h-10 w-20 justify-between px-2" :aria-label="__('Category color')">
                            @if ($color)
                                <flux:badge :color="$color" size="sm" rounded class="size-5 p-0" />
                                <span class="sr-only">{{ Str::headline($color) }}</span>
                            @else
                                <flux:icon.swatch class="size-4" />
                                <span class="sr-only">{{ __('No color') }}</span>
                            @endif
                            <flux:icon.chevron-down class="size-3" />
                        </flux:button>
                        <flux:menu class="!min-w-0">
                            <div class="grid grid-cols-4 gap-0.5 p-1">
                                @foreach (ExpenseCategoryAppearance::COLORS as $colorOption)
                                    <flux:menu.item
                                        as="button"
                                        type="button"
                                        :aria-label="Str::headline($colorOption)"
                                        :aria-pressed="$color === $colorOption"
                                        :title="Str::headline($colorOption)"
                                        wire:click="$set('color', '{{ $colorOption }}')"
                                        class="!h-8 !w-8 !justify-center !p-1"
                                    >
                                        <flux:badge :color="$colorOption" size="sm" rounded class="size-5 justify-center p-0">
                                            @if ($color === $colorOption)
                                                <flux:icon.check class="size-3" />
                                            @endif
                                        </flux:badge>
                                    </flux:menu.item>
                                @endforeach
                                <flux:menu.item as="button" type="button" :aria-label="__('No color')" :title="__('No color')" wire:click="$set('color', null)" class="!h-8 !w-8 !justify-center !p-1">
                                    <flux:icon.x-mark class="size-4" />
                                </flux:menu.item>
                            </div>
                        </flux:menu>
                    </flux:dropdown>
                    <flux:error name="color" />
                </flux:field>

                <flux:field>
                    <flux:dropdown position="bottom" align="start">
                        <flux:button type="button" variant="outline" class="!h-10 w-20 justify-between px-2" :aria-label="__('Category icon')">
                            @if (in_array($icon, ExpenseCategoryAppearance::ICONS, true))
                                <flux:icon :name="$icon" class="size-4" />
                                <span class="sr-only">{{ Str::headline($icon) }}</span>
                            @else
                                <flux:icon.tag class="size-4" />
                                <span class="sr-only">{{ __('No icon') }}</span>
                            @endif
                            <flux:icon.chevron-down class="size-3" />
                        </flux:button>
                        <flux:menu class="!min-w-0">
                            <div class="grid grid-cols-4 gap-0.5 p-1">
                                @foreach (ExpenseCategoryAppearance::ICONS as $iconOption)
                                    <flux:menu.item
                                        as="button"
                                        type="button"
                                        :aria-label="Str::headline($iconOption)"
                                        :aria-pressed="$icon === $iconOption"
                                        :title="Str::headline($iconOption)"
                                        wire:click="$set('icon', '{{ $iconOption }}')"
                                        class="!h-8 !w-8 !justify-center !p-1"
                                    >
                                        <flux:icon :name="$iconOption" class="size-4" />
                                    </flux:menu.item>
                                @endforeach
                                <flux:menu.item as="button" type="button" :aria-label="__('No icon')" :title="__('No icon')" wire:click="$set('icon', null)" class="!h-8 !w-8 !justify-center !p-1">
                                    <flux:icon.x-mark class="size-4" />
                                </flux:menu.item>
                            </div>
                        </flux:menu>
                    </flux:dropdown>
                    <flux:error name="icon" />
                </flux:field>
            </div>

            <flux:button class="w-full" type="submit" icon="plus" variant="primary" color="zinc">
                {{ __('Add Category') }}
            </flux:button>
        </form>

        <flux:separator class="my-4" />

        <div class="flex flex-wrap items-center gap-2">
            @forelse ($this->categories as $category)
                <button
                    type="button"
                    wire:key="category-{{ $category->id }}"
                    wire:click="openRenameModal('{{ $category->id }}')"
                    aria-label="{{ __('Edit Category') }}: {{ $category->name }}"
                    data-category-edit-trigger
                    class="inline-flex max-w-full cursor-pointer items-center rounded-full focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-zinc-500 focus-visible:ring-offset-2"
                >
                    <flux:badge
                        size="lg"
                        rounded
                        :color="in_array($category->color, ExpenseCategoryAppearance::COLORS, true) ? $category->color : 'zinc'"
                        :icon="in_array($category->icon, ExpenseCategoryAppearance::ICONS, true) ? $category->icon : null"
                        data-category-preview
                        class="max-w-full truncate px-3 py-2 text-base"
                    >
                        {{ $category->name }}
                    </flux:badge>
                </button>
            @empty
                <flux:text class="w-full py-4 text-center">{{ __('No categories yet') }}</flux:text>
            @endforelse
        </div>
    </flux:card>

    <flux:modal name="edit-category" class="w-90 md:w-auto">
        <div class="space-y-6">
            <flux:heading size="lg">{{ __('Edit Category') }}</flux:heading>

            <form wire:submit="renameCategory" class="space-y-4">
                <div class="grid grid-cols-[minmax(0,1fr)_auto_auto] items-end gap-2">
                    <flux:field class="min-w-0">
                        <flux:label class="sr-only">{{ __('Category name') }}</flux:label>
                        <flux:input name="editingName" wire:model="editingName" required />
                        <flux:error name="editingName" />
                    </flux:field>

                <flux:field>
                    <flux:dropdown position="bottom" align="start">
                        <flux:button type="button" variant="outline" class="!h-10 w-20 justify-between px-2" :aria-label="__('Category color')">
                            @if ($editingColor)
                                <flux:badge :color="$editingColor" size="sm" rounded class="size-5 p-0" />
                                <span class="sr-only">{{ Str::headline($editingColor) }}</span>
                            @else
                                <flux:icon.swatch class="size-4" />
                                <span class="sr-only">{{ __('No color') }}</span>
                            @endif
                            <flux:icon.chevron-down class="size-3" />
                        </flux:button>
                        <flux:menu class="!min-w-0">
                            <div class="grid grid-cols-4 gap-0.5 p-1">
                                @foreach (ExpenseCategoryAppearance::COLORS as $colorOption)
                                    <flux:menu.item
                                        as="button"
                                        type="button"
                                        :aria-label="Str::headline($colorOption)"
                                        :aria-pressed="$editingColor === $colorOption"
                                        :title="Str::headline($colorOption)"
                                        wire:click="$set('editingColor', '{{ $colorOption }}')"
                                        class="!h-8 !w-8 !justify-center !p-1"
                                    >
                                        <flux:badge :color="$colorOption" size="sm" rounded class="size-5 justify-center p-0">
                                            @if ($editingColor === $colorOption)
                                                <flux:icon.check class="size-3" />
                                            @endif
                                        </flux:badge>
                                    </flux:menu.item>
                                @endforeach
                                <flux:menu.item as="button" type="button" :aria-label="__('No color')" :title="__('No color')" wire:click="$set('editingColor', null)" class="!h-8 !w-8 !justify-center !p-1">
                                    <flux:icon.x-mark class="size-4" />
                                </flux:menu.item>
                            </div>
                        </flux:menu>
                    </flux:dropdown>
                    <flux:error name="editingColor" />
                </flux:field>

                <flux:field>
                    <flux:dropdown position="bottom" align="start">
                        <flux:button type="button" variant="outline" class="!h-10 w-20 justify-between px-2" :aria-label="__('Category icon')">
                            @if (in_array($editingIcon, ExpenseCategoryAppearance::ICONS, true))
                                <flux:icon :name="$editingIcon" class="size-4" />
                                <span class="sr-only">{{ Str::headline($editingIcon) }}</span>
                            @else
                                <flux:icon.tag class="size-4" />
                                <span class="sr-only">{{ __('No icon') }}</span>
                            @endif
                            <flux:icon.chevron-down class="size-3" />
                        </flux:button>
                        <flux:menu class="!min-w-0">
                            <div class="grid grid-cols-4 gap-0.5 p-1">
                                @foreach (ExpenseCategoryAppearance::ICONS as $iconOption)
                                    <flux:menu.item
                                        as="button"
                                        type="button"
                                        :aria-label="Str::headline($iconOption)"
                                        :aria-pressed="$editingIcon === $iconOption"
                                        :title="Str::headline($iconOption)"
                                        wire:click="$set('editingIcon', '{{ $iconOption }}')"
                                        class="!h-8 !w-8 !justify-center !p-1"
                                    >
                                        <flux:icon :name="$iconOption" class="size-4" />
                                    </flux:menu.item>
                                @endforeach
                                <flux:menu.item as="button" type="button" :aria-label="__('No icon')" :title="__('No icon')" wire:click="$set('editingIcon', null)" class="!h-8 !w-8 !justify-center !p-1">
                                    <flux:icon.x-mark class="size-4" />
                                </flux:menu.item>
                            </div>
                        </flux:menu>
                    </flux:dropdown>
                    <flux:error name="editingIcon" />
                </flux:field>

                </div>

                <div class="flex justify-between gap-2">
                    <flux:button class="w-full" variant="outline" color="zinc" x-on:click="$flux.modal('edit-category').close()">{{ __('Cancel') }}</flux:button>
                    <flux:button class="w-full" type="button" variant="danger" wire:click="openDeleteModal('{{ $editingCategoryId }}')">{{ __('Delete') }}</flux:button>
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