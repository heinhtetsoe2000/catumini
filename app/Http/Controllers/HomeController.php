<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $expenses = Expense::query()->currentUser()->today()->latest('id')->get();
        $total = $expenses->sum('amount');

        return view('home', compact('expenses', 'total'));
    }

    public function addExpense(): View
    {
        return view('add-expense');
    }

    public function storeExpense(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:255'],
            'spent_on' => ['required', 'date'],
        ]);

        Expense::create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('home')->with('success', 'Expense added successfully');
    }

    public function editExpense(Request $request, Expense $expense): View
    {
        abort_unless($expense->isOwnedBy($request->user()), 403);

        return view('edit-expense', compact('expense'));
    }

    public function updateExpense(Request $request, Expense $expense): RedirectResponse
    {
        abort_unless($expense->isOwnedBy($request->user()), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:255'],
            'spent_on' => ['required', 'date'],
        ]);

        $expense->update($validated);

        return redirect()->route('home')->with('success', 'Expense updated successfully');
    }

    public function destroyExpense(Request $request, Expense $expense): RedirectResponse
    {
        abort_unless($expense->isOwnedBy($request->user()), 403);

        $expense->delete();

        return redirect()->route('home')->with('success', 'Expense deleted successfully');
    }
}
