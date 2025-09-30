<?php

namespace App\Filament\SystemAdmin\Pages;

use App\Filament\SystemAdmin\Widgets\SaasMetricsWidget;
use App\Filament\SystemAdmin\Widgets\StoreOverviewWidget;
use App\Filament\SystemAdmin\Widgets\SubscriptionStatsWidget;
use App\Filament\SystemAdmin\Widgets\SystemHealthWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static string $view = 'filament.system-admin.pages.dashboard';

    public function getWidgets(): array
    {
        return [
            SaasMetricsWidget::class,
            StoreOverviewWidget::class,
            SubscriptionStatsWidget::class,
            SystemHealthWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return [
            'md' => 2,
            'xl' => 3,
        ];
    }
}