<?php

use App\Http\Controllers\Category\CategoryBuyerController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Category\CategoryProductController;
use App\Http\Controllers\Category\CategorySellerController;
use App\Http\Controllers\Category\CategoryTransactionController;
use Illuminate\Support\Facades\Route;


Route::resource('categories', CategoryController::class, ['except' => ['edit', 'create']]);
Route::resource('categories.products', CategoryProductController::class, ['only' => ['index']]);
Route::resource('categories.sellers', CategorySellerController::class, ['only' => ['index']]);
Route::resource('categories.transactions', CategoryTransactionController::class, ['only' => ['index']]);
Route::resource('categories.buyers', CategoryBuyerController::class, ['only' => ['index']]);