<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function() {
    return redirect()->route('dashboard');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Google OAuth Routes
Route::get('/auth/google', [LoginController::class, 'redirectToGoogle'])->name('auth.google')->middleware('guest');
Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback'])->middleware('guest');

// Application Routes (Require Auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [TransactionController::class, 'dashboard'])->name('dashboard');
    Route::post('/accounts/switch', [AccountController::class, 'switchAccount'])->name('accounts.switch');
    Route::resource('accounts', AccountController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('transactions', TransactionController::class);
    Route::get('/transactions/create/select-category', [CategoryController::class, 'picker'])->name('categories.picker');
    Route::resource('budgets', BudgetController::class);
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/categories', [ReportController::class, 'categories'])->name('reports.categories');
    Route::get('/reports/ledger', [ReportController::class, 'ledger'])->name('reports.ledger');
});
