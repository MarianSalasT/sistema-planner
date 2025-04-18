<?php

namespace App\Providers;

use App\Interfaces\BoardServiceInterface;
use App\Interfaces\ColumnServiceInterface;
use App\Interfaces\CardServiceInterface;
use App\Services\BoardService;
use App\Services\ColumnService;
use App\Services\CardService;
use App\Interfaces\AuthServiceInterface;
use App\Services\AuthService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BoardServiceInterface::class, BoardService::class);
        $this->app->bind(ColumnServiceInterface::class, ColumnService::class);
        $this->app->bind(CardServiceInterface::class, CardService::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);  
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
