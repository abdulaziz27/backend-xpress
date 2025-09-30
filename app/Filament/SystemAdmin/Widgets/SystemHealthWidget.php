<?php

namespace App\Filament\SystemAdmin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class SystemHealthWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';
    protected static ?int $sort = 4;

    protected function getStats(): array
    {
        return [
            Stat::make('Database', $this->getDatabaseStatus())
                ->description('Connection status')
                ->descriptionIcon('heroicon-m-circle-stack')
                ->color($this->getDatabaseStatus() === 'Connected' ? 'success' : 'danger'),

            Stat::make('Cache', $this->getCacheStatus())
                ->description('Redis status')
                ->descriptionIcon('heroicon-m-bolt')
                ->color($this->getCacheStatus() === 'Connected' ? 'success' : 'danger'),

            Stat::make('Queue', $this->getQueueStatus())
                ->description('Job processing')
                ->descriptionIcon('heroicon-m-queue-list')
                ->color($this->getQueueStatus() === 'Running' ? 'success' : 'warning'),

            Stat::make('Storage', $this->getStorageStatus())
                ->description('Disk usage')
                ->descriptionIcon('heroicon-m-server')
                ->color($this->getStorageUsage() < 80 ? 'success' : 'warning'),
        ];
    }

    private function getDatabaseStatus(): string
    {
        try {
            DB::connection()->getPdo();
            return 'Connected';
        } catch (\Exception $e) {
            return 'Disconnected';
        }
    }

    private function getCacheStatus(): string
    {
        try {
            Cache::store('redis')->put('health_check', 'ok', 1);
            return Cache::store('redis')->get('health_check') === 'ok' ? 'Connected' : 'Error';
        } catch (\Exception $e) {
            return 'Disconnected';
        }
    }

    private function getQueueStatus(): string
    {
        try {
            $size = Queue::size();
            return $size !== null ? 'Running' : 'Stopped';
        } catch (\Exception $e) {
            return 'Error';
        }
    }

    private function getStorageStatus(): string
    {
        $usage = $this->getStorageUsage();
        return $usage . '% used';
    }

    private function getStorageUsage(): float
    {
        $bytes = disk_free_space('/');
        $total = disk_total_space('/');
        
        if ($bytes === false || $total === false) {
            return 0;
        }
        
        return round((($total - $bytes) / $total) * 100, 1);
    }
}