<?php

use Illuminate\Support\Facades\Route;

// Group all module routes under /api
Route::prefix('v1')->group(function () {
    require __DIR__ . '/product.php';
    require __DIR__ . '/seller.php';
    require __DIR__ . '/user.php';
    require __DIR__ . '/buyer.php';
    require __DIR__ . '/transaction.php';
    require __DIR__ . '/category.php';

});
