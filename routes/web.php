<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/add-expense', [HomeController::class, 'addExpense'])->name('add-expense');
    Route::post('/add-expense', [HomeController::class, 'storeExpense'])->name('home.store');
    Route::get('/expenses/{expense}/edit', [HomeController::class, 'editExpense'])->name('expenses.edit');
    Route::put('/expenses/{expense}', [HomeController::class, 'updateExpense'])->name('expenses.update');
    Route::delete('/expenses/{expense}', [HomeController::class, 'destroyExpense'])->name('expenses.destroy');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
