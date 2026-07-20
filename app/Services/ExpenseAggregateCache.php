<?php

namespace App\Services;

use App\Models\Expense;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Cache;

class ExpenseAggregateCache
{
    public const TTL_SECONDS = 86400;

    public function dayKey(int $ownerId, CarbonInterface $day): string
    {
        return sprintf('owner:%d:day:%s', $ownerId, $this->yangonDay($day));
    }

    public function monthKey(int $ownerId, CarbonInterface $month): string
    {
        return sprintf('owner:%d:month:%s', $ownerId, $this->yangonMonth($month));
    }

    public function dayTotal(int $ownerId, CarbonInterface $day): int
    {
        return (int) Cache::remember(
            $this->dayKey($ownerId, $day),
            self::TTL_SECONDS,
            fn (): int => (int) Expense::query()
                ->where('user_id', $ownerId)
                ->whereDate('spent_on', $this->yangonDay($day))
                ->sum('amount')
        );
    }

    /**
     * @return array<string, int> Spend date (Y-m-d) => day sum
     */
    public function monthDayTotals(int $ownerId, CarbonInterface $month): array
    {
        return Cache::remember(
            $this->monthKey($ownerId, $month),
            self::TTL_SECONDS,
            function () use ($ownerId, $month): array {
                $anchor = Carbon::parse($this->yangonDay($month), $this->timezone());

                $totals = Expense::query()
                    ->where('user_id', $ownerId)
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

    public function invalidateForExpense(Expense $expense): void
    {
        $ownerId = (int) $expense->user_id;
        $dates = [$this->carbonFromSpentOn($expense->spent_on)];

        if ($expense->wasChanged('spent_on')) {
            $original = $expense->getOriginal('spent_on');

            if ($original !== null) {
                $dates[] = $this->carbonFromSpentOn($original);
            }
        }

        $this->invalidateSpendDates($ownerId, ...$dates);
    }

    public function invalidateSpendDates(int $ownerId, CarbonInterface ...$dates): void
    {
        $forgottenDays = [];
        $forgottenMonths = [];

        foreach ($dates as $date) {
            $day = $this->yangonDay($date);
            $month = $this->yangonMonth($date);

            if (! isset($forgottenDays[$day])) {
                Cache::forget($this->dayKey($ownerId, $date));
                $forgottenDays[$day] = true;
            }

            if (! isset($forgottenMonths[$month])) {
                Cache::forget($this->monthKey($ownerId, $date));
                $forgottenMonths[$month] = true;
            }
        }
    }

    private function yangonDay(CarbonInterface $day): string
    {
        return Carbon::parse($day->format('Y-m-d'), $this->timezone())->toDateString();
    }

    private function yangonMonth(CarbonInterface $month): string
    {
        return Carbon::parse($month->format('Y-m-d'), $this->timezone())->format('Y-m');
    }

    private function carbonFromSpentOn(mixed $spentOn): CarbonInterface
    {
        return Carbon::parse($spentOn, $this->timezone());
    }

    private function timezone(): string
    {
        return (string) config('app.timezone');
    }
}
