<?php

use App\Http\Controllers\Buyer\BuyerController;
use App\Http\Controllers\Buyer\BuyerTransactionController;
use Illuminate\Support\Facades\Route;



Route::resource('buyers', BuyerController::class, ['only' => ['index', 'show']]);
Route::resource('buyers.transactions', BuyerTransactionController::class, ['only' => ['index']]);