<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore;

use Illuminate\Support\ServiceProvider;
use MyPlatform\EcommerceCore\Modules\Product\Repositories\Contracts\ProductRepositoryInterface;
use MyPlatform\EcommerceCore\Modules\Product\Repositories\ProductRepository;
use MyPlatform\EcommerceCore\Modules\Payment\Factories\PaymentFactory;

class EcommerceCoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind Repositories
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);

        // Register PaymentFactory as singleton
        $this->app->singleton(PaymentFactory::class);

        // Bind Services
        $this->app->singleton(\MyPlatform\EcommerceCore\Services\Branding\BrandManager::class);
        $this->app->singleton(\MyPlatform\EcommerceCore\Modules\Auth\Services\OTPService::class);
        $this->app->singleton(\MyPlatform\EcommerceCore\Services\ModuleManager::class);
        $this->app->singleton(\MyPlatform\EcommerceCore\Modules\Integration\Services\IntegrationService::class);
        $this->app->singleton(\MyPlatform\EcommerceCore\Modules\Customer\Services\CustomerRewardService::class);
        $this->app->singleton(\MyPlatform\EcommerceCore\Modules\Analytics\Services\AnalyticsService::class);
        
        // Bind Shipping Strategy (Default to Aramex for now, can be dynamic)
        $this->app->bind(
            \MyPlatform\EcommerceCore\Modules\Delivery\Services\ShippingStrategyInterface::class,
            \MyPlatform\EcommerceCore\Modules\Delivery\Strategies\AramexShippingStrategy::class
        );

        // Merge Config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/ecommerce.php', 'ecommerce'
        );
    }

    public function boot(): void
    {
        // Load Migrations
        $this->loadMigrationsFrom(__DIR__ . '/Modules/Product/Database/Migrations');
        $this->loadMigrationsFrom(__DIR__ . '/Modules/Order/Database/Migrations');
        $this->loadMigrationsFrom(__DIR__ . '/Modules/Marketing/Database/Migrations');
        $this->loadMigrationsFrom(__DIR__ . '/Modules/Review/Database/Migrations');
        $this->loadMigrationsFrom(__DIR__ . '/Modules/Integration/Database/Migrations');
        $this->loadMigrationsFrom(__DIR__ . '/Modules/Customer/Database/Migrations');
        $this->loadMigrationsFrom(__DIR__ . '/Modules/CMS/Database/Migrations');
        $this->loadMigrationsFrom(__DIR__ . '/Modules/Analytics/Database/Migrations');

        // Load Views

        // Load Views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'ecommerce-core');

        // Publish Config

        // Publish Config
        $this->publishes([
            __DIR__ . '/../config/ecommerce.php' => config_path('ecommerce.php'),
        ], 'ecommerce-config');

        // Register Events
        \Illuminate\Support\Facades\Event::listen(
            \MyPlatform\EcommerceCore\Events\PaymentSuccessful::class,
            \MyPlatform\EcommerceCore\Listeners\UpdateStock::class
        );
        
        \Illuminate\Support\Facades\Event::listen(
            \MyPlatform\EcommerceCore\Events\PaymentSuccessful::class,
            \MyPlatform\EcommerceCore\Listeners\RewardLoyalty::class
        );

        \Illuminate\Support\Facades\Event::listen(
            \MyPlatform\EcommerceCore\Events\PaymentSuccessful::class,
            \MyPlatform\EcommerceCore\Listeners\TrackAnalytics::class
        );

        \Illuminate\Support\Facades\Event::listen(
            \MyPlatform\EcommerceCore\Events\PaymentSuccessful::class,
            \MyPlatform\EcommerceCore\Listeners\NotifyAdminOfLargeOrder::class
        );

        \Illuminate\Support\Facades\Event::listen(
            \MyPlatform\EcommerceCore\Events\OrderCreated::class,
            \MyPlatform\EcommerceCore\Listeners\SendNotification::class
        );
    }
}
