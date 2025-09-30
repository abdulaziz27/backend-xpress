<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Member;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StoreStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $store = auth()->user()->store;
        
        $todayOrders = Order::where('store_id', $store->id)
            ->whereDate('created_at', Carbon::today())
            ->count();
            
        $todayRevenue = Order::where('store_id', $store->id)
            ->whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->sum('total_amount');
            
        $totalProducts = Product::where('store_id', $store->id)->count();
        $totalMembers = Member::where('store_id', $store->id)->count();

        return [
            Stat::make('Today\'s Orders', $todayOrders)
                ->description('Orders processed today')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('success'),

            Stat::make('Today\'s Revenue', '$' . number_format($todayRevenue, 2))
                ->description('Revenue generated today')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Total Products', $totalProducts)
                ->description('Products in catalog')
                ->descriptionIcon('heroicon-m-cube')
                ->color('info'),

            Stat::make('Total Members', $totalMembers)
                ->description('Registered customers')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
        ];
    }
}