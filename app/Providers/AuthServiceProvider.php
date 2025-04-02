<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    public function boot(): void
    {
        $this->registerPolicies();

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
    }
}
