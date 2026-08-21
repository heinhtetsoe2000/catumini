<?php

use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Livewire\Livewire;

test('new owner defaults to income tracking off', function () {
    $user = User::factory()->create();

    expect($user->income_tracking)->toBeFalse();

    $html = $this->actingAs($user)->get(route('home'))->getContent();

    expect($html)->not->toContain(route('income'))
        ->and($html)->not->toContain(__('Income'));
});

test('owner can enable income tracking instantly from profile', function () {
    $user = User::factory()->create(['income_tracking' => false]);

    $this->actingAs($user)
        ->post(route('preferences.income-tracking'), ['income_tracking' => 1])
        ->assertRedirect();

    expect($user->fresh()->income_tracking)->toBeTrue();

    $html = $this->actingAs($user->fresh())->get(route('home'))->getContent();

    expect($html)->toContain(__('Income'))
        ->and($html)->toContain(route('income'));
});

test('disabling income tracking hides ui but keeps income rows', function () {
    $user = User::factory()->withIncomeTracking()->create();

    $income = Income::factory()->create([
        'user_id' => $user->id,
        'name' => 'Salary',
        'amount' => 500000,
    ]);

    $this->actingAs($user)
        ->post(route('preferences.income-tracking'), ['income_tracking' => 0])
        ->assertRedirect();

    expect($user->fresh()->income_tracking)->toBeFalse()
        ->and(Income::query()->find($income->id))->not->toBeNull();

    $this->actingAs($user->fresh())
        ->get(route('home'))
        ->assertSuccessful()
        ->assertDontSee(__('Available'));

    $this->actingAs($user->fresh())
        ->get(route('income'))
        ->assertRedirect(route('home'));
});

test('re-enabling income tracking restores income ui', function () {
    $user = User::factory()->withIncomeTracking()->create();

    Income::factory()->create([
        'user_id' => $user->id,
        'name' => 'Salary',
        'amount' => 500000,
    ]);

    $this->actingAs($user)
        ->post(route('preferences.income-tracking'), ['income_tracking' => 0]);

    $this->actingAs($user->fresh())
        ->post(route('preferences.income-tracking'), ['income_tracking' => 1]);

    $this->actingAs($user->fresh())
        ->get(route('income'))
        ->assertSuccessful()
        ->assertSee('Salary')
        ->assertSee('500,000');
});

test('home shows available strip and today total when tracking is on', function () {
    $user = User::factory()->withIncomeTracking()->create();

    Income::factory()->create([
        'user_id' => $user->id,
        'amount' => 500000,
    ]);

    Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 100000,
        'spent_on' => now()->toDateString(),
    ]);

    Livewire::actingAs($user)
        ->test('pages::home')
        ->assertSet('available', 400000)
        ->assertSee(__('Available'))
        ->assertSee('400,000')
        ->assertSee('100,000');
});

test('history shows month income strip, month spent total, and available when tracking is on', function () {
    $user = User::factory()->withIncomeTracking()->create();

    Income::factory()->create([
        'user_id' => $user->id,
        'amount' => 300000,
        'received_on' => now()->startOfMonth()->toDateString(),
    ]);

    Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 50000,
        'spent_on' => now()->startOfMonth()->toDateString(),
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee(now()->format('F') . ' ' . __('Income'))
        ->assertSee('300,000')
        ->assertSee(__('Available'))
        ->assertSee('250,000')
        ->assertSee('50,000');
});

test('strips are absent when income tracking is off', function () {
    $user = User::factory()->create(['income_tracking' => false]);

    Expense::factory()->create(['user_id' => $user->id, 'amount' => 1000]);
    Income::factory()->create(['user_id' => $user->id, 'amount' => 5000]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertDontSee(__('Total Income'))
        ->assertDontSee(__('Available'));
});
