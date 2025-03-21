<?php

use App\Http\Controllers\User\UserController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
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


Route::get('/test-redis', function () {
    dump(Redis::connection()->ping());
    // Cache::put('test_key', 'hello from redis', 60); 
    dump(config('cache.default'));

    // dd(Cache::get('test_key')); // Should print "hello world"
    return Cache::get('test_key');
});
