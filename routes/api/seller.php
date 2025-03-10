<?php

use App\Http\Controllers\Seller\SellerController;
use Illuminate\Support\Facades\Route;


Route::resource('sellers', SellerController::class, ['only' => ['index', 'show']]);