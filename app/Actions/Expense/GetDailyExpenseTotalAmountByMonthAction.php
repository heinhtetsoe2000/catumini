<?php

namespace App\Actions\Expense;

use App\Helpers\ExpenseCache;
use App\Helpers\Timezone;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class GetDailyExpenseTotalAmountByMonthAction
{
    public function execute(?Carbon $spentOn = null): array
    {
        $spentOn ??= now();

        $userId = Auth::id();
        
        return Cache::remember(
            ExpenseCache::getDailyTotalAmountByMonthKey($userId, $spentOn),
            ExpenseCache::TTL_SECONDS,
            function () use ($userId, $spentOn): array {
                $anchor = Carbon::parse(Timezone::apply($spentOn));

                $totals = Expense::query()
                    ->where('user_id', $userId)
                    ->whereYear('spent_on', $anchor->year)
                    ->whereMonth('spent_on', $anchor->month)
                    ->selectRaw('spent_on, SUM(amount) as total')
                    ->groupBy('spent_on')
                    ->orderBy('spent_on')
                    ->pluck('total', 'spent_on');

                return $totals
                    ->mapWithKeys(fn (mixed $total, mixed $spentOn): array => [
                        Carbon::parse($spentOn)->toDateString() => (int) $total,
                    ])
                    ->all();
            }
        );
    }
}
