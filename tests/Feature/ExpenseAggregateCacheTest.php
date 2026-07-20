<?php

use App\Models\Expense;
use App\Models\User;
use App\Services\ExpenseAggregateCache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

beforeEach(function () {
    Cache::flush();
});

test('day total is stored on cold read and reused on warm read', function () {
    $user = User::factory()->create();
    $cache = app(ExpenseAggregateCache::class);
    $day = now();

    Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 1500,
        'spent_on' => $day->toDateString(),
    ]);

    Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 500,
        'spent_on' => $day->toDateString(),
    ]);

    expect($cache->dayTotal($user->id, $day))->toBe(2000)
        ->and(Cache::has($cache->dayKey($user->id, $day)))->toBeTrue();

    Expense::withoutEvents(function () use ($user, $day): void {
        Expense::factory()->create([
            'user_id' => $user->id,
            'amount' => 9999,
            'spent_on' => $day->toDateString(),
        ]);
    });

    expect($cache->dayTotal($user->id, $day))->toBe(2000);
});

test('month day totals are stored on cold read and omit empty days', function () {
    $user = User::factory()->create();
    $cache = app(ExpenseAggregateCache::class);
    $month = now();

    Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 1000,
        'spent_on' => $month->copy()->startOfMonth()->toDateString(),
    ]);

    Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 2500,
        'spent_on' => $month->copy()->startOfMonth()->addDays(2)->toDateString(),
    ]);

    $totals = $cache->monthDayTotals($user->id, $month);

    expect($totals)
        ->toHaveCount(2)
        ->toHaveKey($month->copy()->startOfMonth()->toDateString())
        ->and($totals[$month->copy()->startOfMonth()->toDateString()])->toBe(1000)
        ->and($totals[$month->copy()->startOfMonth()->addDays(2)->toDateString()])->toBe(2500)
        ->and(Cache::has($cache->monthKey($user->id, $month)))->toBeTrue();
});

test('creating an expense invalidates day and month keys', function () {
    $user = User::factory()->create();
    $cache = app(ExpenseAggregateCache::class);
    $day = now();

    Cache::put($cache->dayKey($user->id, $day), 1, ExpenseAggregateCache::TTL_SECONDS);
    Cache::put($cache->monthKey($user->id, $day), ['x' => 1], ExpenseAggregateCache::TTL_SECONDS);

    Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 400,
        'spent_on' => $day->toDateString(),
    ]);

    expect(Cache::has($cache->dayKey($user->id, $day)))->toBeFalse()
        ->and(Cache::has($cache->monthKey($user->id, $day)))->toBeFalse();
});

test('updating amount invalidates day and month keys', function () {
    $user = User::factory()->create();
    $cache = app(ExpenseAggregateCache::class);
    $day = now();

    $expense = Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 400,
        'spent_on' => $day->toDateString(),
    ]);

    Cache::put($cache->dayKey($user->id, $day), 400, ExpenseAggregateCache::TTL_SECONDS);
    Cache::put($cache->monthKey($user->id, $day), [$day->toDateString() => 400], ExpenseAggregateCache::TTL_SECONDS);

    $expense->update(['amount' => 800]);

    expect(Cache::has($cache->dayKey($user->id, $day)))->toBeFalse()
        ->and(Cache::has($cache->monthKey($user->id, $day)))->toBeFalse();
});

test('deleting an expense invalidates day and month keys', function () {
    $user = User::factory()->create();
    $cache = app(ExpenseAggregateCache::class);
    $day = now();

    $expense = Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 400,
        'spent_on' => $day->toDateString(),
    ]);

    Cache::put($cache->dayKey($user->id, $day), 400, ExpenseAggregateCache::TTL_SECONDS);
    Cache::put($cache->monthKey($user->id, $day), [$day->toDateString() => 400], ExpenseAggregateCache::TTL_SECONDS);

    $expense->delete();

    expect(Cache::has($cache->dayKey($user->id, $day)))->toBeFalse()
        ->and(Cache::has($cache->monthKey($user->id, $day)))->toBeFalse();
});

test('cross-month spend date edit invalidates both days and both months', function () {
    $user = User::factory()->create();
    $cache = app(ExpenseAggregateCache::class);

    $from = Carbon::parse(now()->startOfMonth()->subDay()->toDateString());
    $to = Carbon::parse(now()->startOfMonth()->toDateString());

    $expense = Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 1200,
        'spent_on' => $from->toDateString(),
    ]);

    Cache::put($cache->dayKey($user->id, $from), 1200, ExpenseAggregateCache::TTL_SECONDS);
    Cache::put($cache->dayKey($user->id, $to), 0, ExpenseAggregateCache::TTL_SECONDS);
    Cache::put($cache->monthKey($user->id, $from), [$from->toDateString() => 1200], ExpenseAggregateCache::TTL_SECONDS);
    Cache::put($cache->monthKey($user->id, $to), [], ExpenseAggregateCache::TTL_SECONDS);

    $expense->update(['spent_on' => $to->toDateString()]);

    expect(Cache::has($cache->dayKey($user->id, $from)))->toBeFalse()
        ->and(Cache::has($cache->dayKey($user->id, $to)))->toBeFalse()
        ->and(Cache::has($cache->monthKey($user->id, $from)))->toBeFalse()
        ->and(Cache::has($cache->monthKey($user->id, $to)))->toBeFalse();
});

test('owners do not share aggregate cache keys', function () {
    $ownerA = User::factory()->create();
    $ownerB = User::factory()->create();
    $cache = app(ExpenseAggregateCache::class);
    $day = now();

    Expense::factory()->create([
        'user_id' => $ownerA->id,
        'amount' => 1000,
        'spent_on' => $day->toDateString(),
    ]);

    Expense::factory()->create([
        'user_id' => $ownerB->id,
        'amount' => 5000,
        'spent_on' => $day->toDateString(),
    ]);

    expect($cache->dayTotal($ownerA->id, $day))->toBe(1000)
        ->and($cache->dayTotal($ownerB->id, $day))->toBe(5000)
        ->and($cache->dayKey($ownerA->id, $day))->not->toBe($cache->dayKey($ownerB->id, $day));
});

test('today total stays correct after write when cache was warm', function () {
    $user = User::factory()->create();
    $cache = app(ExpenseAggregateCache::class);
    $day = now();

    Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 1000,
        'spent_on' => $day->toDateString(),
    ]);

    expect($cache->dayTotal($user->id, $day))->toBe(1000);

    Livewire::actingAs($user)
        ->test('pages::home')
        ->assertSet('total', 1000)
        ->set('name', 'Snack')
        ->set('amount', 500)
        ->set('spent_on', $day->toDateString())
        ->call('save')
        ->assertSet('total', 1500);
});

test('monthly history totals match database after writes', function () {
    $user = User::factory()->create();

    Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 1000,
        'spent_on' => now()->startOfMonth()->toDateString(),
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('1,000 Ks');

    Expense::factory()->create([
        'user_id' => $user->id,
        'amount' => 2000,
        'spent_on' => now()->startOfMonth()->toDateString(),
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('3,000 Ks');
});
