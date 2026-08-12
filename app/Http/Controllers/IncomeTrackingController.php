<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class IncomeTrackingController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'income_tracking' => ['required', 'boolean'],
        ]);

        $request->user()?->update([
            'income_tracking' => $validated['income_tracking'],
        ]);

        return redirect()->back();
    }
}
