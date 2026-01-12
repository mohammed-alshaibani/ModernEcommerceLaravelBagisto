<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $brandManager = app(\MyPlatform\EcommerceCore\Services\Branding\BrandManager::class);

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName($brandManager->getBrandName())
            ->brandLogo($brandManager->getLogoUrl())
            ->font($brandManager->getFontFamily())
            ->colors([
                'primary' => $brandManager->getPrimaryColor(),
            ])
            // Discovery for App Resources
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            // Discovery for Package Resources (Modular)
            ->discoverResources(
                in: base_path('../../packages/ecommerce-core/src/Filament/Resources'),
                for: 'MyPlatform\\EcommerceCore\\Filament\\Resources'
            )
            ->discoverPages(
                in: base_path('../../packages/ecommerce-core/src/Filament/Pages'),
                for: 'MyPlatform\\EcommerceCore\\Filament\\Pages'
            )
            ->discoverWidgets(
                in: base_path('../../packages/ecommerce-core/src/Filament/Widgets'),
                for: 'MyPlatform\\EcommerceCore\\Filament\\Widgets'
            )
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
