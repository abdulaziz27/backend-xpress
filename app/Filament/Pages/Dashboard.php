<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\StoreStatsWidget;
use App\Filament\Widgets\SalesOverviewWidget;
use App\Filament\Widgets\RecentOrdersWidget;
use App\Filament\Widgets\TopProductsWidget;
use App\Filament\Widgets\CashierStatsWidget;
use App\Services\NavigationService;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getWidgets(): array
    {
        $user = Auth::user();
        $widgets = [];

        if (!$user) {
            return $widgets;
        }

        // Role-based widget assignment
        if ($user->hasRole('owner')) {
            $widgets = [
                StoreStatsWidget::class,
                SalesOverviewWidget::class,
                RecentOrdersWidget::class,
                TopProductsWidget::class,
            ];
        } elseif ($user->hasRole('manager')) {
            $widgets = [
                StoreStatsWidget::class,
                SalesOverviewWidget::class,
                RecentOrdersWidget::class,
            ];
        } elseif ($user->hasRole('cashier')) {
            $widgets = [
                CashierStatsWidget::class,
                RecentOrdersWidget::class,
            ];
        }

        return $widgets;
    }

    public function getColumns(): int | string | array
    {
        return [
            'md' => 2,
            'xl' => 3,
        ];
    }
}