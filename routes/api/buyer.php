<?php

use App\Http\Controllers\Buyer\BuyerController;
use App\Http\Controllers\Buyer\BuyerProductController;
use App\Http\Controllers\Buyer\BuyerSellerController;
use App\Http\Controllers\Buyer\BuyerTransactionController;
use Illuminate\Support\Facades\Route;



Route::resource('buyers', BuyerController::class, ['only' => ['index', 'show']]);
Route::resource('buyers.transactions', BuyerTransactionController::class, ['only' => ['index']]);
Route::resource('buyers.products', BuyerProductController::class, ['only' => ['index']]);
Route::resource('buyers.sellers', BuyerSellerController::class, ['only' => ['index']]);