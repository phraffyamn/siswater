<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        // Behind the platform's TLS-terminating proxy (e.g. Railway) generated
        // URLs must use https so assets, redirects and form actions stay secure.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
