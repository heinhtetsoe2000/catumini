<?php

namespace App\Observers;

use App\Models\Income;
use App\Services\ExpenseAggregateCache;

class IncomeObserver
{
    public function __construct(public ExpenseAggregateCache $expenseAggregateCache) {}

    public function created(Income $income): void
    {
        $this->expenseAggregateCache->invalidateForIncome($income);
    }

    public function updated(Income $income): void
    {
        $this->expenseAggregateCache->invalidateForIncome($income);
    }

    public function deleted(Income $income): void
    {
        $this->expenseAggregateCache->invalidateForIncome($income);
    }
}
