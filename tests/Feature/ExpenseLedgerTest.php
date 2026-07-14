<?php

use App\Models\Expense;
use App\Models\User;

test('user can create an expense with default spend date of today', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('home.store'), [
        'name' => 'Coffee',
        'amount' => 3500,
        'description' => 'Morning',
        'spent_on' => now()->toDateString(),
    ]);

    $response->assertRedirect(route('home'));

    $expense = Expense::query()->where('name', 'Coffee')->first();

    expect($expense)
        ->amount->toBe(3500)
        ->user_id->toBe($user->id)
        ->and($expense->spent_on->toDateString())->toBe(now()->toDateString());
});

test('user can create an expense with a backdated spend date', function () {
    $user = User::factory()->create();
    $spentOn = now()->subDays(2)->toDateString();

    $this->actingAs($user)->post(route('home.store'), [
        'name' => 'Lunch',
        'amount' => 8000,
        'spent_on' => $spentOn,
    ])->assertRedirect(route('home'));

    expect(Expense::query()->where('name', 'Lunch')->first()->spent_on->toDateString())
        ->toBe($spentOn);
});

test('today view only includes expenses spent today', function () {
    $user = User::factory()->create();

    Expense::factory()->create([
        'user_id' => $user->id,
        'name' => 'Today lunch',
        'amount' => 5000,
        'spent_on' => now()->toDateString(),
    ]);

    Expense::factory()->create([
        'user_id' => $user->id,
        'name' => 'Yesterday lunch',
        'amount' => 4000,
        'spent_on' => now()->subDay()->toDateString(),
    ]);

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('Today lunch');
    $response->assertDontSee('Yesterday lunch');
    $response->assertSee('5,000 Ks');
});

test('monthly view groups by spend date and handles empty month', function () {
    $user = User::factory()->create();

    $empty = $this->actingAs($user)->get(route('dashboard'));
    $empty->assertSuccessful();
    $empty->assertSee('0 Ks');
    $empty->assertSee('Avg: 0 Ks');

    Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 1000,
        'spent_on' => now()->startOfMonth()->toDateString(),
    ]);

    Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 2000,
        'spent_on' => now()->startOfMonth()->toDateString(),
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));
    $response->assertSuccessful();
    $response->assertSee('3,000 Ks');
});

test('owner can update their expense', function () {
    $user = User::factory()->create();
    $expense = Expense::factory()->create([
        'user_id' => $user->id,
        'name' => 'Old',
        'amount' => 1000,
        'spent_on' => now()->toDateString(),
    ]);

    $this->actingAs($user)->put(route('expenses.update', $expense), [
        'name' => 'New',
        'amount' => 2500,
        'description' => 'Fixed',
        'spent_on' => now()->subDay()->toDateString(),
    ])->assertRedirect(route('home'));

    expect($expense->fresh())
        ->name->toBe('New')
        ->amount->toBe(2500)
        ->description->toBe('Fixed');
});

test('owner can hard delete their expense', function () {
    $user = User::factory()->create();
    $expense = Expense::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->delete(route('expenses.destroy', $expense))
        ->assertRedirect(route('home'));

    $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
});

test('user cannot update or delete another users expense', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $expense = Expense::factory()->create([
        'user_id' => $owner->id,
        'name' => 'Secret',
        'amount' => 999,
    ]);

    $this->actingAs($intruder)->put(route('expenses.update', $expense), [
        'name' => 'Hacked',
        'amount' => 1,
        'spent_on' => now()->toDateString(),
    ])->assertForbidden();

    $this->actingAs($intruder)
        ->delete(route('expenses.destroy', $expense))
        ->assertForbidden();

    expect($expense->fresh()->name)->toBe('Secret');
});
