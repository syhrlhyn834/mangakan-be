<?php

namespace App\Providers;

use Illuminate\Support\Carbon;
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
    public function boot()
    {
           //URL::forceScheme('https');
           // Set default timezone to Asia/Jakarta
    Carbon::setLocale('id'); // Optional: set locale to Indonesia
    date_default_timezone_set('Asia/Jakarta');

    }
}
