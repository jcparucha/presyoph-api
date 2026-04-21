<?php

namespace App\Providers;

use App\Contracts\ProductHandlerInterface;
use App\Services\ProductHandlerService;
use Illuminate\Support\ServiceProvider;

class HandlerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->bind(
            ProductHandlerInterface::class,
            ProductHandlerService::class,
        );
    }
}
