<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Force HTTPS URLs when running on Replit or when FORCE_HTTPS is enabled
        if (config('app.force_https') || request()->header('x-forwarded-proto') === 'https' || str_contains(request()->getHost(), 'replit.dev')) {
            URL::forceScheme('https');
        }
    }
}