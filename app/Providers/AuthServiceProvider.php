<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        //Passport::routes();
        Passport::tokensCan([
            'customer' => 'User',
            'admin' => 'User',
            'seller'=>'User',
            'delivery'=>'User'
        ]);
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
        Passport::personalAccessClient(
            config('passport.personal_access_client.id')
        );
        Passport::personalAccessClient(
            config('passport.personal_access_client.secret')
        );
        Passport::cookie('level_token');
        Passport::enablePasswordGrant(); 
    }
}
