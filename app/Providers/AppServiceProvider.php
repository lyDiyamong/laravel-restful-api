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
