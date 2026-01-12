<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Services\Branding;

use Illuminate\Support\Facades\Config;

class BrandManager
{
    public function getBrandName(): string
    {
        return Config::get('filament-brand.name', 'My Platform');
    }

    public function getPrimaryColor(): array
    {
        // Default to a vibrant blue if not configured
        return Config::get('filament-brand.colors.primary', [
            50 => '#eff6ff',
            100 => '#dbeafe',
            200 => '#bfdbfe',
            300 => '#93c5fd',
            400 => '#60a5fa',
            500 => '#3b82f6',
            600 => '#2563eb',
            700 => '#1d4ed8',
            800 => '#1e40af',
            900 => '#1e3a8a',
            950 => '#172554',
        ]);
    }

    public function getFontFamily(): string
    {
        return Config::get('filament-brand.font', 'Cairo');
    }

    public function getLogoUrl(): ?string
    {
        return Config::get('filament-brand.logo_url');
    }

    public function isRtl(): bool
    {
        return Config::get('filament-brand.rtl', true);
    }
}
