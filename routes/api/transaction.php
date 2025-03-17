<?php

use App\Http\Controllers\Transaction\TransactionCategoryController;
use App\Http\Controllers\Transaction\TransactionController;
use Illuminate\Support\Facades\Route;

Route::resource('transactions', TransactionController::class);
Route::resource('transactions.categories', TransactionCategoryController::class, ['only' => ['index']]);