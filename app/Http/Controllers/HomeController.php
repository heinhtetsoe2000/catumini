<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Data\ExpenseData;
use Illuminate\Support\Collection;
use Spatie\LaravelData\DataCollection;

class HomeController extends Controller
{
    public function index()
    {
        $hasExpenses = random_int(0, 1);

        $expenses = $hasExpenses ? ExpenseData::collect([
                new ExpenseData('Chicken eggs (10)', 5000),
                new ExpenseData('Lunch', 3000),
            ], Collection::class) : collect();

        $total = $expenses->sum(fn(ExpenseData $expense) => $expense->amount);

        return view('home', compact('expenses', 'total'));
    }
}
