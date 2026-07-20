<?php

namespace App\Observers;

use App\Models\Expense;
use App\Services\ExpenseAggregateCache;

class ExpenseObserver
{
    public function __construct(public ExpenseAggregateCache $expenseAggregateCache) {}

    public function created(Expense $expense): void
    {
        $this->expenseAggregateCache->invalidateForExpense($expense);
    }

    public function updated(Expense $expense): void
    {
        $this->expenseAggregateCache->invalidateForExpense($expense);
    }

    public function deleted(Expense $expense): void
    {
        $this->expenseAggregateCache->invalidateForExpense($expense);
    }
}
