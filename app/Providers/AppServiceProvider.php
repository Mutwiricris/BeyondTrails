<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        if ($this->app->environment('production') || request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }
    }
}
