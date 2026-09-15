<?php

namespace App\Helpers;

use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Cache;

class ExpenseCache
{
    public const TTL_SECONDS = 86400;

    public static function getDailyTotalAmountKey(string $ownerId, CarbonInterface $day): string
    {
        return sprintf('owner:%s:day:%s', $ownerId, Timezone::apply($day));
    }

    public static function getDailyTotalAmountByMonthKey(string $ownerId, CarbonInterface $month): string
    {
        return sprintf('owner:%s:month:%s', $ownerId, Timezone::apply($month, 'Y-m'));
    }
}
