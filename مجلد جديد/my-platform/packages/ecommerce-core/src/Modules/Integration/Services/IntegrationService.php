<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Integration\Services;

use MyPlatform\EcommerceCore\Modules\Integration\Models\IntegrationConfig;
use Illuminate\Support\Facades\Cache;

class IntegrationService
{
    /**
     * Get active config for a specific module and provider
     */
    public function getConfig(string $module, ?string $provider = null): array
    {
        $query = IntegrationConfig::where('module', $module)
            ->where('is_active', true);

        if ($provider) {
            $query->where('provider', $provider);
        }

        $configs = $query->get();

        $result = [];
        foreach ($configs as $config) {
            $result[$config->key_name] = $config->encrypted_value;
        }

        return $result;
    }

    /**
     * Get the active provider code for a module
     */
    public function getActiveProvider(string $module): ?string
    {
        return IntegrationConfig::where('module', $module)
            ->where('is_active', true)
            ->value('provider');
    }
}
