<?php

namespace App\Actions\Expense;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GetExpensesByDayAction
{
    public function execute(?Carbon $spentOn = null): Collection
    {
        $spentOn ??= now();
        
        return Expense::ofDay($spentOn)->currentUser()->orderBy('created_at', 'desc')->get();
    }
}
