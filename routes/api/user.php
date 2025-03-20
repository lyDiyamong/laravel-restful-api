<?php

use App\Http\Controllers\User\UserController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;

Route::resource('users', UserController::class , ['except' => ['create', 'edit']]);
    // ->middleware(IsAdmin::class);

// Route::name('verify')
//     ->post('users/verify/', [UserController::class, 'verifyOtp']);
 

Route::prefix('users')->group(function () {
    Route::post('/verify', [UserController::class, 'verifyOtp']);
    Route::get('/resend-otp/{email}', [UserController::class, 'resendOtp'])
        // 5 attempts per minute;
    ->middleware('throttle:5,1');
});

