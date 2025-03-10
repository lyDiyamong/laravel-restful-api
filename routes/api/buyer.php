<?php

use App\Http\Controllers\Buyer\BuyerController;
use Illuminate\Support\Facades\Route;



Route::resource('buyers', BuyerController::class, ['only' => ['index', 'show']]);