<?php

namespace App\Filament\SystemAdmin\Widgets;

use App\Models\Store;
use App\Models\Subscription;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SaasMetricsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $totalStores = Store::count();
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $totalUsers = User::count();
        $monthlyRevenue = Subscription::where('status', 'active')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->sum('plans.price');

        return [
            Stat::make('Total Stores', $totalStores)
                ->description('Registered stores')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('success'),

            Stat::make('Active Subscriptions', $activeSubscriptions)
                ->description('Currently active')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('primary'),

            Stat::make('Total Users', $totalUsers)
                ->description('All platform users')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Monthly Revenue', '$' . number_format($monthlyRevenue, 2))
                ->description('Current MRR')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
        ];
    }
}