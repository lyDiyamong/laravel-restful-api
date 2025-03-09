<?php

use Illuminate\Support\Facades\Route;

// Group all module routes under /api
Route::prefix('v1')->group(function () {
    // require __DIR__ . '/modules/auth.php';
    require __DIR__ . '/product.php';
    // require __DIR__ . '/modules/user.php';
    // require __DIR__ . '/modules/admin.php';
});
