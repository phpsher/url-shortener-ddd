<?php

namespace App\Providers;

use App\Contracts\Services\UrlServiceContract;
use App\Services\UrlService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->bind(
            UrlServiceContract::class,
            UrlService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
