<?php

namespace App\Providers;

use Exception;
use App\Models\User;
use App\Models\Product;
use App\Mail\UserCreated;
use App\Exceptions\Handler;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Debug\ExceptionHandler;

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
        Passport::tokensExpireIn(now()->addMinutes(15));
        Passport::refreshTokensExpireIn(now()->addDays(7));
        Passport::enablePasswordGrant();

        Passport::tokensCan([
            'read-user' => 'Read user profile',
            'write-user' => 'Modify user profile',
        ]);
    
        Passport::setDefaultScope([
            'read-user'
        ]);


        Product::updated(function (Product $product) {
            if ($product->quantity == 0 && $product->isAvailable()){
                $product->status = Product::UNAVAILABLE_PRODUCT;

                $product->save();
            }
        }
    );

    }
}
