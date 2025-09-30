<?php

namespace App\Providers\Filament;

use App\Http\Middleware\TenantScopeMiddleware;
use App\Http\Middleware\FilamentResourceAccessMiddleware;
use App\Services\NavigationService;
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
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('POS Xpress - Store Admin')
            ->brandLogo(asset('img/logo-jf.png'))
            ->favicon(asset('favicon.ico'))
            ->colors([
                'primary' => Color::Blue,
                'gray' => Color::Slate,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
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
                TenantScopeMiddleware::class,
                FilamentResourceAccessMiddleware::class,
            ])
            ->navigationGroups(NavigationService::getNavigationGroupsForUser())
            ->renderHook(
                'panels::head.end',
                fn (): string => '<link rel="stylesheet" href="' . asset('css/filament-roles.css') . '">'
            )
            ->renderHook(
                'panels::body.end',
                fn (): string => '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        // Role-based UI customizations
                        const userRole = "' . (auth()->user()?->roles->first()?->name ?? '') . '";
                        document.body.classList.add("role-" + userRole);
                        
                        // Add role badge to user menu
                        const userMenu = document.querySelector(".fi-dropdown-trigger");
                        if (userMenu && userRole) {
                            const badge = document.createElement("span");
                            badge.className = "role-badge " + userRole;
                            badge.textContent = userRole.replace("_", " ");
                            userMenu.appendChild(badge);
                        }
                    });
                </script>'
            );
    }
}
