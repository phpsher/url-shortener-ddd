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
        $this->app->bind(
            \App\Repositories\Interfaces\UrlRepositoryInterface::class,
            \App\Repositories\UrlRepository::class
        );

        $this->app->bind(
            \App\Services\Interfaces\UrlServiceInterface::class,
            \App\Services\UrlService::class
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
