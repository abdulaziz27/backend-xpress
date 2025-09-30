<?php

namespace App\Filament\SystemAdmin\Widgets;

use App\Models\Store;
use App\Models\Subscription;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class StoreOverviewWidget extends ChartWidget
{
    protected static ?string $heading = 'Store Growth';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $months = collect(range(0, 11))->map(function ($month) {
            return Carbon::now()->subMonths($month)->format('M Y');
        })->reverse();

        $storeData = $months->map(function ($month) {
            return Store::whereMonth('created_at', Carbon::parse($month)->month)
                ->whereYear('created_at', Carbon::parse($month)->year)
                ->count();
        });

        $subscriptionData = $months->map(function ($month) {
            return Subscription::where('status', 'active')
                ->whereMonth('created_at', Carbon::parse($month)->month)
                ->whereYear('created_at', Carbon::parse($month)->year)
                ->count();
        });

        return [
            'datasets' => [
                [
                    'label' => 'New Stores',
                    'data' => $storeData->values()->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
                [
                    'label' => 'New Subscriptions',
                    'data' => $subscriptionData->values()->toArray(),
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}