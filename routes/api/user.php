<?php

use App\Http\Middleware\IsAdmin;
use App\Jobs\SendWelcomeEmailJob;
use App\Listeners\SendWelcomeEmail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\UserController;

    // ->middleware(IsAdmin::class);

// Route::name('verify')
//     ->post('users/verify/', [UserController::class, 'verifyOtp']);
 
Route::middleware('auth:api')->group(function () {

    Route::resource('users', UserController::class , ['except' => ['create', 'edit', 'index']]);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/test-queue', function () {

        $user = auth()->user(); // Get the authenticated user

        SendWelcomeEmailJob::dispatch($user);

        return "Job dispatched!";
    });

});


Route::resource('users', UserController::class , ['only' => ['index']]);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/refresh', [AuthController::class, 'refreshToken'])->name('refresh');


Route::prefix('auth')->group(function () {
    Route::post('/verify', [AuthController::class, 'verifyOtp']);
    Route::get('/resend-otp/{email}', [AuthController::class, 'resendOtp'])
        // 5 attempts per minute;
    ->middleware('throttle:5,1');
});


Route::get('/test-redis', function () {
    dump(Redis::connection()->ping());
    Cache::put('test_key', 'hello from redis', 60); 
    dump(config('cache.default'));

    // dd(Cache::get('test_key')); // Should print "hello world"
    return Cache::get('test_key');
});
