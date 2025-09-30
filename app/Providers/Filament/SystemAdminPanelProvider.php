<?php

namespace App\Providers\Filament;

use App\Http\Middleware\RoleMiddleware;
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

class SystemAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('system-admin')
            ->path('system-admin')
            ->login()
            ->brandName('POS Xpress - System Admin')
            ->brandLogo(asset('img/logo-jf.png'))
            ->favicon(asset('favicon.ico'))
            ->colors([
                'primary' => Color::Red,
                'gray' => Color::Slate,
            ])
            ->discoverResources(in: app_path('Filament/SystemAdmin/Resources'), for: 'App\\Filament\\SystemAdmin\\Resources')
            ->discoverPages(in: app_path('Filament/SystemAdmin/Pages'), for: 'App\\Filament\\SystemAdmin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/SystemAdmin/Widgets'), for: 'App\\Filament\\SystemAdmin\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
                RoleMiddleware::class . ':admin_sistem',
            ])
            ->navigationGroups([
                'SaaS Management',
                'System Monitoring',
                'User Management',
                'Analytics',
            ]);
    }
}