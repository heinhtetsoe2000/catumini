<?php

use Livewire\Component;
use App\Models\Expense;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;

new class extends Component
{
    public Expense $expense;

    public string $name;

    public int $amount;

    public string $spent_on = '';

    public ?string $description = '';

    public ?string $category_id = null;

    public function mount(Expense $expense)
    {
        $this->name = $expense->name;
        $this->amount = $expense->amount;
        $this->spent_on = $expense->spent_on->toDateString();
        $this->description = $expense->description;
        $this->category_id = $expense->category_id;
    }

    #[Computed]
    public function categories(): Collection
    {
        return auth()->user()->expenseCategories()->orderBy('name')->get();
    }

    public function update()
    {
        abort_unless($this->expense->isOwnedBy(auth()->user()), 403);
        
        $validated = $this->validate([
            'name' => 'required|max:255',
            'amount' => 'required|numeric|min:0',
            'spent_on' => 'required|date',
            'description' => 'nullable|string',
            'category_id' => ['nullable', 'uuid', Rule::exists('expense_categories', 'id')->where('user_id', auth()->id())],
        ]);

        $validated['name'] = trim($validated['name']);
        $validated['description'] = trim($validated['description']);

        $this->expense->update($validated);
        $this->dispatch('expense-updated');

        $this->modal('edit-expense-' . $this->expense->id)->close();
    }

    public function openEditModal()
    {
        $this->modal('edit-expense-' . $this->expense->id)->show();
    }

    public function openDeleteModal()
    {
        $this->modal('delete-expense-' . $this->expense->id)->show();
    }

    public function delete()
    {
        abort_unless($this->expense->isOwnedBy(auth()->user()), 403);

        $this->expense->delete();
        $this->modal('delete-expense-' . $this->expense->id)->close();

        $this->dispatch('deleted');
    }
};
?>

<div>
    <button wire:click="openEditModal" class="w-full flex items-center justify-between gap-2 p-4 data-loading:opacity-50 [&[data-loading]_.icon]:animate-spin">
        <flux:icon.loading class="icon mx-auto not-in-data-loading:hidden" />
        <div class="in-data-loading:hidden">
            <flux:text class="text-left text-lg font-bold truncate max-w-[10rem] sm:max-w-[13rem] text-black dark:text-white">{{ $name }}</flux:text>
            @if ($description)
                <flux:text class="text-left text-sm truncate max-w-[10rem] sm:max-w-[13rem] text-black dark:text-white">{{ $description }}</flux:text>
            @endif
            @if ($expense->category)
                <flux:badge size="sm" color="zinc" data-expense-category>{{ $expense->category->name }}</flux:badge>
            @endif
        </div>
        <flux:text class="text-left text-lg font-bold text-black dark:text-white in-data-loading:hidden">{{ number_format($amount) }} {{ __('Ks') }}</flux:text>
    </button>

    <flux:modal name="edit-expense-{{ $expense->id }}" class="w-90 md:w-auto">
        <div class="space-y-6">
            <flux:heading size="lg">{{ __('Edit Expense') }}</flux:heading>

            <form wire:submit="update" class="space-y-4">
                @csrf

                <flux:input name="name" wire:model="name" :placeholder="__('Name')" required />

                <div class="flex items-center justify-between gap-2">
                    <flux:input name="amount" type="number" wire:model="amount" :placeholder="__('Amount (Ks)')" min="0" step="1" required />
                    <flux:input name="spent_on" type="date" wire:model="spent_on" required />
                </div>

                <flux:field>
                    <flux:label>{{ __('Category') }}</flux:label>
                    <flux:select name="category_id" wire:model="category_id">
                        <flux:select.option value="">{{ __('No category') }}</flux:select.option>
                        @foreach ($this->categories as $category)
                            <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="category_id" />
                </flux:field>

                <flux:textarea name="description" wire:model="description" :placeholder="__('Description')">{{ $this->description }}</flux:textarea>

                <div class="flex justify-between gap-2">
                    <flux:button class="w-full" variant="danger" wire:click="openDeleteModal">{{ __('Delete') }}</flux:button>
                    <flux:button class="w-full" variant="primary" color="zinc" type="submit">{{ __('Update') }}</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <flux:modal name="delete-expense-{{ $expense->id }}" class="w-90 md:w-auto">
        <div class="space-y-6">
            <flux:heading size="lg">{{ __('Delete') }} "{{ $name }}"</flux:heading>

            <form wire:submit="delete" class="space-y-4">
                @csrf

                <div class="flex justify-between gap-2">
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button class="w-full" variant="danger" type="submit">{{ __('Delete') }}</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
