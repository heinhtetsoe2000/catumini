<?php

namespace App\Http\Controllers;

use App\Services\ExpenseAggregateCache;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, ExpenseAggregateCache $expenseAggregateCache): View
    {
        $dayTotals = $expenseAggregateCache->monthDayTotals(
            $request->user()->id,
            now()
        );

        /** @var Collection<string, int> $expenses */
        $expenses = collect($dayTotals)
            ->sortKeysDesc()
            ->mapWithKeys(fn (int $amount, string $spentOn): array => [
                $spentOn => $amount,
            ]);

        $total = (int) $expenses->sum();
        $average = $expenses->count() > 0 ? (int) round($expenses->avg()) : 0;

        $incomeTracking = (bool) $request->user()->income_tracking;
        $monthReceived = 0;
        $monthSpent = $total;
        $available = 0;

        if ($incomeTracking) {
            $monthReceived = $expenseAggregateCache->monthIncomeTotal($request->user()->id, now());
            $available = $expenseAggregateCache->available($request->user()->id);
        }

        return view('dashboard', compact(
            'expenses',
            'total',
            'average',
            'incomeTracking',
            'monthReceived',
            'monthSpent',
            'available',
        ));
    }
}
