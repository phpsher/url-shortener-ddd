<?php

namespace App\Application\Providers;

use App\Application\Services\UrlService;
use App\Domain\Click\Repository\ClickRepositoryInterface;
use App\Domain\Url\Repositories\UrlRepositoryInterface;
use App\Domain\Url\Services\UrlServiceInterface;
use App\Infrastructure\Persistence\Click\Repository\ClickRepository;
use App\Infrastructure\Persistence\Url\Repository\UrlRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->bind(
            UrlRepositoryInterface::class,
            UrlRepository::class,
        );

        $this->app->bind(
            UrlServiceInterface::class,
            UrlService::class,
        );

        $this->app->bind(
            ClickRepositoryInterface::class,
            ClickRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
