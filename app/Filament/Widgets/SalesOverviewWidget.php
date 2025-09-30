<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SalesOverviewWidget extends ChartWidget
{
    protected static ?string $heading = 'Sales Overview (Last 7 Days)';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $store = auth()->user()->store;
        
        $days = collect(range(0, 6))->map(function ($day) {
            return Carbon::now()->subDays($day)->format('M d');
        })->reverse();

        $salesData = $days->map(function ($day) use ($store) {
            return Order::where('store_id', $store->id)
                ->where('status', 'completed')
                ->whereDate('created_at', Carbon::parse($day))
                ->sum('total_amount');
        });

        $orderData = $days->map(function ($day) use ($store) {
            return Order::where('store_id', $store->id)
                ->whereDate('created_at', Carbon::parse($day))
                ->count();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Revenue ($)',
                    'data' => $salesData->values()->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Orders',
                    'data' => $orderData->values()->toArray(),
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $days->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
        ];
    }
}