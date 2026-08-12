<?php

use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use App\Services\ExpenseAggregateCache;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

test('available is zero before first income despite expenses', function () {
    $user = User::factory()->withIncomeTracking()->create();
    $cache = app(ExpenseAggregateCache::class);

    Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 50000,
    ]);

    expect($cache->available($user->id))->toBe(0);
});

test('available reflects honest math after first income', function () {
    $user = User::factory()->withIncomeTracking()->create();
    $cache = app(ExpenseAggregateCache::class);

    Income::factory()->create([
        'user_id' => $user->id,
        'amount' => 500000,
    ]);

    Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 100000,
    ]);

    expect($cache->available($user->id))->toBe(400000);
});

test('global totals are cached on cold read and reused on warm read', function () {
    $user = User::factory()->create();
    $cache = app(ExpenseAggregateCache::class);

    Income::factory()->create([
        'user_id' => $user->id,
        'amount' => 3000,
    ]);

    Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 1000,
    ]);

    expect($cache->totalReceived($user->id))->toBe(3000)
        ->and($cache->totalSpent($user->id))->toBe(1000)
        ->and(Cache::has($cache->totalReceivedKey($user->id)))->toBeTrue()
        ->and(Cache::has($cache->totalSpentKey($user->id)))->toBeTrue();

    Income::withoutEvents(fn () => Income::factory()->create([
        'user_id' => $user->id,
        'amount' => 9999,
    ]));

    expect($cache->totalReceived($user->id))->toBe(3000);
});

test('creating income invalidates global and month income keys', function () {
    $user = User::factory()->create();
    $cache = app(ExpenseAggregateCache::class);
    $day = now();

    Cache::put($cache->totalReceivedKey($user->id), 1, ExpenseAggregateCache::TTL_SECONDS);
    Cache::put($cache->monthIncomeKey($user->id, $day), 1, ExpenseAggregateCache::TTL_SECONDS);

    Income::factory()->create([
        'user_id' => $user->id,
        'amount' => 400,
        'received_on' => $day->toDateString(),
    ]);

    expect(Cache::has($cache->totalReceivedKey($user->id)))->toBeFalse()
        ->and(Cache::has($cache->monthIncomeKey($user->id, $day)))->toBeFalse();
});

test('creating expense invalidates global spent key', function () {
    $user = User::factory()->create();
    $cache = app(ExpenseAggregateCache::class);
    $day = now();

    Cache::put($cache->totalSpentKey($user->id), 1, ExpenseAggregateCache::TTL_SECONDS);

    Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 400,
        'spent_on' => $day->toDateString(),
    ]);

    expect(Cache::has($cache->totalSpentKey($user->id)))->toBeFalse();
});

test('cross-month received date edit invalidates both month income keys', function () {
    $user = User::factory()->create();
    $cache = app(ExpenseAggregateCache::class);

    $from = Carbon\Carbon::parse(now()->startOfMonth()->subDay()->toDateString());
    $to = Carbon\Carbon::parse(now()->startOfMonth()->toDateString());

    $income = Income::factory()->create([
        'user_id' => $user->id,
        'amount' => 1200,
        'received_on' => $from->toDateString(),
    ]);

    Cache::put($cache->monthIncomeKey($user->id, $from), 1200, ExpenseAggregateCache::TTL_SECONDS);
    Cache::put($cache->monthIncomeKey($user->id, $to), 0, ExpenseAggregateCache::TTL_SECONDS);

    $income->update(['received_on' => $to->toDateString()]);

    expect(Cache::has($cache->monthIncomeKey($user->id, $from)))->toBeFalse()
        ->and(Cache::has($cache->monthIncomeKey($user->id, $to)))->toBeFalse();
});
