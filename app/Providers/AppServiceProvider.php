<?php

namespace App\Providers;

use App\Exceptions\Handler;
use App\Mail\UserCreated;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Passport;
// use App\Models\Passport\AuthCode;
// use App\Models\Passport\Client;
// use App\Models\Passport\PersonalAccessClient;
// use App\Models\Passport\RefreshToken;
// use App\Models\Passport\Token;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->singleton(ExceptionHandler::class, Handler::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Passport::ignoreRoutes();
        Passport::loadKeysFrom(storage_path('oauth'));
        Passport::tokensExpireIn(now()->addMinutes(2));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::enablePasswordGrant();

        Passport::tokensCan([
            'read-user' => 'Read user profile',
            'write-user' => 'Modify user profile',
        ]);
    
        Passport::setDefaultScope([
            'read-user'
        ]);


        User::created(function (User $user) {
            try {
                Mail::to($user->email)->send(new UserCreated($user));
                Log::info("Mail sent successfully to: {$user->email}");
            } catch (Exception $e) {
                Log::error("Failed to send mail to {$user->email}: " . $e->getMessage());
            }
        });

        Product::updated(function (Product $product) {
            if ($product->quantity == 0 && $product->isAvailable()){
                $product->status = Product::UNAVAILABLE_PRODUCT;

                $product->save();
            }
        }
    );

    }
}
