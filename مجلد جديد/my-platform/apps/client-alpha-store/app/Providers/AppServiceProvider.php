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
        // Swap implementation
        $this->app->bind(
            \MyPlatform\EcommerceCore\Modules\Product\Repositories\Contracts\ProductRepositoryInterface::class,
            \App\Repositories\CustomProductRepository::class
        );
    }
}
