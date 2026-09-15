<?php

namespace App\Helpers;

use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Cache;

class Timezone
{
    public static function apply(CarbonInterface $day, string $format = 'Y-m-d'): string
    {
        return Carbon::parse($day->format($format), self::timezone())->toDateString();
    }

    public static function timezone(): string
    {
        return (string) config('app.timezone');
    }
}
