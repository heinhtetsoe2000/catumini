<?php

use App\Models\Expense;
use App\Models\User;
use Livewire\Livewire;

test('user can create an expense with default spend date of today', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::home')
        ->set('name', 'Coffee')
        ->set('amount', 3500)
        ->set('description', 'Morning')
        ->set('spent_on', now()->toDateString())
        ->call('save')
        ->assertHasNoErrors();

    $expense = Expense::query()->where('name', 'Coffee')->first();

    expect($expense)
        ->amount->toBe(3500)
        ->user_id->toBe($user->id)
        ->and($expense->spent_on->toDateString())->toBe(now()->toDateString());
});

test('user can create an expense with a backdated spend date', function () {
    $user = User::factory()->create();
    $spentOn = now()->subDays(2)->toDateString();

    Livewire::actingAs($user)
        ->test('pages::home')
        ->set('name', 'Lunch')
        ->set('amount', 8000)
        ->set('spent_on', $spentOn)
        ->call('save')
        ->assertHasNoErrors();

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

test('history view groups by spend date and handles empty month', function () {
    $user = User::factory()->create();

    $empty = $this->actingAs($user)->get(route('dashboard'));
    $empty->assertSuccessful();
    $empty->assertSee(__('History'));
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

test('history labels today and yesterday spend groups', function () {
    $user = User::factory()->create();

    Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 1000,
        'spent_on' => now()->toDateString(),
    ]);

    Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 2000,
        'spent_on' => now()->subDay()->toDateString(),
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertSee('Today');
    $response->assertSee('Yesterday');
    $response->assertDontSee(now()->format('D M d'));
    $response->assertDontSee(now()->subDay()->format('D M d'));
});

test('empty home day shows zero spend summary', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::home')
        ->assertSet('total', 0)
        ->assertSet('average', 0)
        ->assertSeeHtml("You haven't spent any.");
});

test('user can navigate to the previous day and see that days expenses', function () {
    $user = User::factory()->create();

    Expense::factory()->create([
        'user_id' => $user->id,
        'name' => 'Today coffee',
        'amount' => 1500,
        'spent_on' => now()->toDateString(),
    ]);

    Expense::factory()->create([
        'user_id' => $user->id,
        'name' => 'Yesterday dinner',
        'amount' => 9000,
        'spent_on' => now()->subDay()->toDateString(),
    ]);

    $component = Livewire::actingAs($user)
        ->test('pages::home')
        ->assertSet('total', 1500)
        ->assertSee('Today coffee');

    expect($component->get('expenses')->pluck('name')->all())
        ->toContain('Today coffee')
        ->not->toContain('Yesterday dinner');

    $component
        ->call('previousDay')
        ->assertSet('total', 9000)
        ->assertSee(now()->subDay()->format('M d, Y'));

    expect($component->get('expenses')->pluck('name')->all())
        ->toContain('Yesterday dinner')
        ->not->toContain('Today coffee');
});

test('home summary compares spend to the monthly daily average', function () {
    $user = User::factory()->create();

    Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 1000,
        'spent_on' => now()->subDay()->toDateString(),
    ]);

    Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 5000,
        'spent_on' => now()->toDateString(),
    ]);

    Livewire::actingAs($user)
        ->test('pages::home')
        ->assertSee('You have spent 2,000 Ks more than the average.');
});
test('owner can update their expense', function () {
    $user = User::factory()->create();
    $expense = Expense::factory()->create([
        'user_id' => $user->id,
        'name' => 'Old',
        'amount' => 1000,
        'spent_on' => now()->toDateString(),
    ]);

    Livewire::actingAs($user)
        ->test('expense.edit', ['expense' => $expense])
        ->set('name', 'New')
        ->set('amount', 2500)
        ->set('description', 'Fixed')
        ->set('spent_on', now()->subDay()->toDateString())
        ->call('update')
        ->assertHasNoErrors();

    expect($expense->fresh())
        ->name->toBe('New')
        ->amount->toBe(2500)
        ->description->toBe('Fixed');
});

test('owner can hard delete their expense', function () {
    $user = User::factory()->create();
    $expense = Expense::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test('expense.edit', ['expense' => $expense])
        ->call('delete');

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

    Livewire::actingAs($intruder)
        ->test('expense.edit', ['expense' => $expense])
        ->set('name', 'Hacked')
        ->set('amount', 1)
        ->set('spent_on', now()->toDateString())
        ->call('update')
        ->assertForbidden();

    Livewire::actingAs($intruder)
        ->test('expense.edit', ['expense' => $expense])
        ->call('delete')
        ->assertForbidden();

    expect($expense->fresh()->name)->toBe('Secret');
});
