<?php

namespace App\Http\Controllers;

use App\Services\ExpenseAggregateCache;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, ExpenseAggregateCache $expenseAggregateCache): View
    {
        $dayTotals = $expenseAggregateCache->monthDayTotals(
            (int) $request->user()->id,
            now()
        );

        /** @var Collection<string, int> $expenses */
        $expenses = collect($dayTotals)
            ->sortKeysDesc()
            ->mapWithKeys(fn (int $amount, string $spentOn): array => [
                Carbon::parse($spentOn)->format('D M d') => $amount,
            ]);

        $total = (int) $expenses->sum();
        $average = $expenses->count() > 0 ? (int) round($expenses->avg()) : 0;

        return view('dashboard', compact('expenses', 'total', 'average'));
    }
}
