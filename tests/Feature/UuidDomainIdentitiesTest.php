<?php

use App\Models\Expense;
use App\Models\User;
use App\Services\ExpenseAggregateCache;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

test('domain tables use uuid primary keys', function () {
    expect(Schema::getColumnType('users', 'id'))->toBeIn(['char', 'varchar', 'uuid'])
        ->and(Schema::getColumnType('expenses', 'id'))->toBeIn(['char', 'varchar', 'uuid']);
});

test('new user and expense receive uuid v7 primary keys', function () {
    $user = User::factory()->create();
    $expense = Expense::factory()->for($user)->create();

    expect(Str::isUuid($user->id))->toBeTrue()
        ->and(Str::isUuid($expense->id))->toBeTrue()
        ->and($expense->user_id)->toBe($user->id);
});

test('expense identity remains stable after update', function () {
    $expense = Expense::factory()->create(['name' => 'Coffee']);

    $originalId = $expense->id;

    $expense->update(['name' => 'Tea']);

    expect($expense->fresh()->id)->toBe($originalId);
});

test('aggregate cache keys use uuid owner ids', function () {
    $user = User::factory()->create();
    $cache = app(ExpenseAggregateCache::class);
    $day = now();

    expect($cache->dayKey($user->id, $day))->toBe('owner:'.$user->id.':day:'.$day->toDateString())
        ->and($cache->monthKey($user->id, $day))->toBe('owner:'.$user->id.':month:'.$day->format('Y-m'));
});

test('expense requires an owner foreign key', function () {
    $user = User::factory()->create();

    Expense::factory()->for($user)->create();

    expect(fn () => Expense::query()->create([
        'name' => 'Orphan',
        'amount' => 100,
        'description' => null,
        'user_id' => (string) Str::uuid7(),
        'spent_on' => now()->toDateString(),
    ]))->toThrow(QueryException::class);
});
