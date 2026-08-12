<?php

use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Livewire\Livewire;

test('owner can create income with default received date of today', function () {
    $user = User::factory()->withIncomeTracking()->create();

    Livewire::actingAs($user)
        ->test('pages::income')
        ->set('name', 'Salary')
        ->set('amount', 350000)
        ->set('description', 'Monthly')
        ->set('received_on', now()->toDateString())
        ->call('save')
        ->assertHasNoErrors();

    $income = Income::query()->where('name', 'Salary')->first();

    expect($income)
        ->amount->toBe(350000)
        ->user_id->toBe($user->id)
        ->and($income->received_on->toDateString())->toBe(now()->toDateString());
});

test('owner can create income with backdated received date', function () {
    $user = User::factory()->withIncomeTracking()->create();
    $receivedOn = now()->subDays(3)->toDateString();

    Livewire::actingAs($user)
        ->test('pages::income')
        ->set('name', 'Refund')
        ->set('amount', 5000)
        ->set('received_on', $receivedOn)
        ->call('save')
        ->assertHasNoErrors();

    expect(Income::query()->where('name', 'Refund')->first()->received_on->toDateString())
        ->toBe($receivedOn);
});

test('income page lists all incomes newest first across months', function () {
    $user = User::factory()->withIncomeTracking()->create();

    Income::factory()->create([
        'user_id' => $user->id,
        'name' => 'Old pay',
        'amount' => 1000,
        'received_on' => now()->subMonth()->toDateString(),
    ]);

    Income::factory()->create([
        'user_id' => $user->id,
        'name' => 'New pay',
        'amount' => 2000,
        'received_on' => now()->toDateString(),
    ]);

    Livewire::actingAs($user)
        ->test('pages::income')
        ->assertSeeInOrder(['New pay', 'Old pay']);
});

test('owner can update their income', function () {
    $user = User::factory()->withIncomeTracking()->create();
    $income = Income::factory()->create([
        'user_id' => $user->id,
        'name' => 'Old',
        'amount' => 1000,
        'received_on' => now()->toDateString(),
    ]);

    Livewire::actingAs($user)
        ->test('income.edit', ['income' => $income])
        ->set('name', 'New')
        ->set('amount', 2500)
        ->set('description', 'Fixed')
        ->set('received_on', now()->subDay()->toDateString())
        ->call('update')
        ->assertHasNoErrors();

    expect($income->fresh())
        ->name->toBe('New')
        ->amount->toBe(2500)
        ->description->toBe('Fixed');
});

test('owner can hard delete their income', function () {
    $user = User::factory()->withIncomeTracking()->create();
    $income = Income::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test('income.edit', ['income' => $income])
        ->call('delete');

    $this->assertDatabaseMissing('incomes', ['id' => $income->id]);
});

test('user cannot update or delete another users income', function () {
    $owner = User::factory()->withIncomeTracking()->create();
    $intruder = User::factory()->withIncomeTracking()->create();
    $income = Income::factory()->create([
        'user_id' => $owner->id,
        'name' => 'Secret',
        'amount' => 999,
    ]);

    Livewire::actingAs($intruder)
        ->test('income.edit', ['income' => $income])
        ->set('name', 'Hacked')
        ->set('amount', 1)
        ->set('received_on', now()->toDateString())
        ->call('update')
        ->assertForbidden();

    Livewire::actingAs($intruder)
        ->test('income.edit', ['income' => $income])
        ->call('delete')
        ->assertForbidden();

    expect($income->fresh()->name)->toBe('Secret');
});

test('negative available shows alarm styling on income page', function () {
    $user = User::factory()->withIncomeTracking()->create();

    Income::factory()->create([
        'user_id' => $user->id,
        'amount' => 10000,
    ]);

    Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 50000,
    ]);

    Livewire::actingAs($user)
        ->test('pages::income')
        ->assertSet('available', -40000)
        ->assertSeeHtml('text-red-600');
});
