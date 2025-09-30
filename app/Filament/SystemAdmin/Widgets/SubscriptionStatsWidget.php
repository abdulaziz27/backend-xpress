<?php

namespace App\Filament\SystemAdmin\Widgets;

use App\Models\Plan;
use App\Models\Subscription;
use Filament\Widgets\ChartWidget;

class SubscriptionStatsWidget extends ChartWidget
{
    protected static ?string $heading = 'Subscription Distribution';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $plans = Plan::withCount(['subscriptions' => function ($query) {
            $query->where('status', 'active');
        }])->get();

        return [
            'datasets' => [
                [
                    'data' => $plans->pluck('subscriptions_count')->toArray(),
                    'backgroundColor' => [
                        'rgb(59, 130, 246)',   // Blue for Basic
                        'rgb(16, 185, 129)',   // Green for Pro
                        'rgb(245, 158, 11)',   // Yellow for Enterprise
                    ],
                    'borderColor' => [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $plans->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}