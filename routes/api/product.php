<?php
use App\Http\Controllers\Product\ProductBuyerController;
use App\Http\Controllers\Product\ProductBuyerTransactionController;
use App\Http\Controllers\Product\ProductCategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\ProductTransactionController;

// Route::prefix('products')->group(function () {
//     Route::get('/', [ProductController::class, 'index']);
//     Route::post('/', [ProductController::class, 'store']);
//     Route::get('/{id}', [ProductController::class, 'show']);
//     Route::put('/{id}', [ProductController::class, 'update']);
//     Route::delete('/{id}', [ProductController::class, 'destroy']);
// });

Route::resource("/products", ProductController::class);
Route::resource("/products.transactions", ProductTransactionController::class, ['only' => ['index']]);
Route::resource("/products.buyers", ProductBuyerController::class, ['only' => ['index']]);
Route::resource("/products.categories", ProductCategoryController::class, ['only' => ['index', 'update', 'destroy']]);
Route::resource("/products.buyers.transactions", ProductBuyerTransactionController::class, ['only' => ['index', 'store']]);