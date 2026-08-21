<?php

use App\Http\Controllers\DisplayPreferenceController;
use App\Http\Controllers\IncomeTrackingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/preferences/display-language', [DisplayPreferenceController::class, 'updateDisplayLanguage'])
    ->name('preferences.display-language');

Route::middleware('auth')->group(function () {
    Route::post('/preferences/appearance', [DisplayPreferenceController::class, 'updateAppearance'])
        ->name('preferences.appearance');
    Route::post('/preferences/income-tracking', [IncomeTrackingController::class, 'update'])
        ->name('preferences.income-tracking');
});

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('home');
    }

    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::livewire('/home', 'pages::home')->name('home');
    Route::livewire('/income', 'pages::income')->name('income');
    Route::livewire('/dashboard', 'pages::dashboard')->name('dashboard');
    Route::livewire('/profile', 'pages::user.profile')->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
