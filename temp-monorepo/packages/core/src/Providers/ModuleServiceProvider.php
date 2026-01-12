<?php

namespace Webkul\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Webkul\Core\Core;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        \Webkul\Core\Models\Channel::class,
        \Webkul\Core\Models\CoreConfig::class,
        \Webkul\Core\Models\Country::class,
        \Webkul\Core\Models\CountryState::class,
        \Webkul\Core\Models\CountryStateTranslation::class,
        \Webkul\Core\Models\CountryTranslation::class,
        \Webkul\Core\Models\Currency::class,
        \Webkul\Core\Models\CurrencyExchangeRate::class,
        \Webkul\Core\Models\Locale::class,
        \Webkul\Core\Models\SubscribersList::class,
        \Webkul\Core\Models\Visit::class,
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        parent::boot();

        $this->app->singleton('core', function () {
            return $this->app->make(Core::class);
        });

        $this->registerModules();
    }

    /**
     * Register all the modules.
     */
    protected function registerModules(): void
    {
        $modules = [
            'admin',
            'auth',
            'core',
            'frontend',
            'modules',
            'payment',
            'shipping',
            'theme',
        ];

        foreach ($modules as $module) {
            $this->registerModule($module);
        }
    }

    /**
     * Register a module.
     */
    protected function registerModule(string $module): void
    {
        $modulePath = base_path("packages/{$module}");

        if (! is_dir($modulePath)) {
            return;
        }

        $directories = array_filter(glob("{$modulePath}/*"), 'is_dir');

        foreach ($directories as $directory) {
            $provider = basename($directory) . 'ServiceProvider';
            $providerPath = "{$directory}/src/Providers/{$provider}.php";

            if (file_exists($providerPath)) {
                $namespace = str_replace('/', '\\', "Webkul\\{$module}\\{$provider}\\Providers\\{$provider}");
                $this->app->register($namespace);
            }
        }
    }
}
