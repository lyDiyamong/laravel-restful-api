<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Auth\SocialAuthController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    // Public authentication routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/refresh', [AuthController::class, 'refreshToken'])->name('refresh');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    // Email verification routes
    Route::post('/verify', [AuthController::class, 'verifyOtp']);
    Route::get('/resend-otp/{email}', [AuthController::class, 'resendOtp'])
        ->middleware('throttle:5,1'); // 5 attempts per minute

    // Social authentication routes
    Route::prefix('social')->group(function () {
        Route::get('/{provider}', [SocialAuthController::class, 'redirectToProvider']);
        Route::get('/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);
    });
});

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/
// Public user routes
Route::get('/users', [UserController::class, 'index']);

// Protected user routes
Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);

    Route::resource('users', UserController::class)->except([
        'create',
        'edit',
        'index'
    ]);
});

// Remove test route in production
if (app()->environment('local')) {
    Route::get('/test-redis', function () {
        dump(Redis::connection()->ping());
        Cache::put('test_key', 'hello from redis', 60);
        return Cache::get('test_key');
    });
}
