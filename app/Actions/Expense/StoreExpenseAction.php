<?php

namespace App\Actions\Expense;

use App\Models\Expense;
use Illuminate\Support\Facades\Auth;

class StoreExpenseAction
{
    public function execute(array $data): Expense
    {
        $data['name'] = trim($data['name']);
        $data['description'] = trim($data['description']);

        return Expense::create([...$data, 'user_id' => Auth::id()]);
    }
}
