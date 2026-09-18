<?php

namespace App\Actions\Expense;

use App\Helpers\ExpenseCache;
use App\Helpers\Timezone;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class GetExpenseTotalAmountByDayAction
{
    public function execute(?Carbon $spentOn = null): int
    {
        $spentOn ??= now();

        $userId = Auth::id();
        
        return (int) Cache::remember(
            ExpenseCache::getDailyTotalAmountKey($userId, $spentOn),
            ExpenseCache::TTL_SECONDS,
            fn (): int => (int) Expense::query()
                ->where('user_id', $userId)
                ->whereDate('spent_on', Timezone::apply($spentOn))
                ->sum('amount')
        );
    }
}
