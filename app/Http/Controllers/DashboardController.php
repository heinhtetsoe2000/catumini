<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $expenses = Expense::query()
            ->currentUser()
            ->monthly()
            ->orderBy('spent_on', 'desc')
            ->get()
            ->groupBy(fn (Expense $expense): string => $expense->spent_on->format('D M d'))
            ->map(fn ($group) => $group->sum('amount'));

        $total = $expenses->sum();
        $average = $expenses->count() > 0 ? (int) round($total / $expenses->count()) : 0;

        return view('dashboard', compact('expenses', 'total', 'average'));
    }
}
