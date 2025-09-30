<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register sync services
        $this->app->singleton(\App\Services\Sync\IdempotencyService::class);
        $this->app->singleton(\App\Services\Sync\SyncPerformanceMonitor::class);
        $this->app->singleton(\App\Services\Sync\SyncValidationService::class);
        $this->app->singleton(\App\Services\Sync\ConflictResolver::class);
        $this->app->singleton(\App\Services\Sync\SyncService::class);
        $this->app->singleton(\App\Services\Sync\SyncReliabilityService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFour();
        
        // Register model observers
        \App\Models\Order::observe(\App\Observers\OrderObserver::class);
    }
}
