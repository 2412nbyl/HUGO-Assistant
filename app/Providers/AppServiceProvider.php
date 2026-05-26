<?php

namespace App\Providers;

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
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.premium');

        $tz = config('app.timezone', 'Asia/Jakarta');
        date_default_timezone_set($tz);
        \Carbon\Carbon::setLocale('id');
    }
}
