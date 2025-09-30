<?php

namespace App\Filament\Navigation;

use App\Services\NavigationService;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\Facades\Auth;

class NavigationBuilder
{
    public static function build(): array
    {
        $user = Auth::user();
        $navigation = [];

        if (!$user) {
            return $navigation;
        }

        // System admin navigation
        if ($user->hasRole('admin_sistem')) {
            $navigation = self::buildSystemAdminNavigation();
        }
        // Store owner navigation
        elseif ($user->hasRole('owner')) {
            $navigation = self::buildStoreOwnerNavigation();
        }
        // Manager navigation
        elseif ($user->hasRole('manager')) {
            $navigation = self::buildManagerNavigation();
        }
        // Cashier navigation
        elseif ($user->hasRole('cashier')) {
            $navigation = self::buildCashierNavigation();
        }

        return $navigation;
    }

    private static function buildSystemAdminNavigation(): array
    {
        return [
            NavigationGroup::make('SaaS Management')
                ->items([
                    NavigationItem::make('Stores')
                        ->icon('heroicon-o-building-storefront')
                        ->url('/system-admin/stores'),
                    NavigationItem::make('Subscriptions')
                        ->icon('heroicon-o-credit-card')
                        ->url('/system-admin/subscriptions'),
                    NavigationItem::make('Plans')
                        ->icon('heroicon-o-rectangle-stack')
                        ->url('/system-admin/plans'),
                ]),
            NavigationGroup::make('User Management')
                ->items([
                    NavigationItem::make('All Users')
                        ->icon('heroicon-o-users')
                        ->url('/system-admin/global-users'),
                    NavigationItem::make('Roles & Permissions')
                        ->icon('heroicon-o-key')
                        ->url('/system-admin/roles'),
                ]),
            NavigationGroup::make('System Monitoring')
                ->items([
                    NavigationItem::make('System Health')
                        ->icon('heroicon-o-heart')
                        ->url('/system-admin/health'),
                    NavigationItem::make('Activity Logs')
                        ->icon('heroicon-o-document-text')
                        ->url('/system-admin/activity-logs'),
                ]),
        ];
    }

    private static function buildStoreOwnerNavigation(): array
    {
        return [
            NavigationGroup::make('Store Management')
                ->items([
                    NavigationItem::make('Staff')
                        ->icon('heroicon-o-users')
                        ->url('/admin/users'),
                    NavigationItem::make('Products')
                        ->icon('heroicon-o-cube')
                        ->url('/admin/products'),
                    NavigationItem::make('Categories')
                        ->icon('heroicon-o-tag')
                        ->url('/admin/categories'),
                ]),
            NavigationGroup::make('Sales & Orders')
                ->items([
                    NavigationItem::make('Orders')
                        ->icon('heroicon-o-shopping-bag')
                        ->url('/admin/orders'),
                    NavigationItem::make('Payments')
                        ->icon('heroicon-o-credit-card')
                        ->url('/admin/payments'),
                    NavigationItem::make('Refunds')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->url('/admin/refunds'),
                ]),
            NavigationGroup::make('Customers')
                ->items([
                    NavigationItem::make('Members')
                        ->icon('heroicon-o-user-group')
                        ->url('/admin/members'),
                    NavigationItem::make('Tables')
                        ->icon('heroicon-o-table-cells')
                        ->url('/admin/tables'),
                ]),
            NavigationGroup::make('Inventory')
                ->items([
                    NavigationItem::make('Stock Levels')
                        ->icon('heroicon-o-archive-box')
                        ->url('/admin/inventory'),
                    NavigationItem::make('Movements')
                        ->icon('heroicon-o-arrows-right-left')
                        ->url('/admin/inventory-movements'),
                ]),
            NavigationGroup::make('Reports')
                ->items([
                    NavigationItem::make('Sales Reports')
                        ->icon('heroicon-o-chart-bar')
                        ->url('/admin/reports/sales'),
                    NavigationItem::make('Inventory Reports')
                        ->icon('heroicon-o-chart-pie')
                        ->url('/admin/reports/inventory'),
                ]),
        ];
    }

    private static function buildManagerNavigation(): array
    {
        return [
            NavigationGroup::make('Sales & Orders')
                ->items([
                    NavigationItem::make('Orders')
                        ->icon('heroicon-o-shopping-bag')
                        ->url('/admin/orders'),
                    NavigationItem::make('Payments')
                        ->icon('heroicon-o-credit-card')
                        ->url('/admin/payments'),
                    NavigationItem::make('Refunds')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->url('/admin/refunds'),
                ]),
            NavigationGroup::make('Customers')
                ->items([
                    NavigationItem::make('Members')
                        ->icon('heroicon-o-user-group')
                        ->url('/admin/members'),
                    NavigationItem::make('Tables')
                        ->icon('heroicon-o-table-cells')
                        ->url('/admin/tables'),
                ]),
            NavigationGroup::make('Inventory')
                ->items([
                    NavigationItem::make('Products')
                        ->icon('heroicon-o-cube')
                        ->url('/admin/products'),
                    NavigationItem::make('Stock Levels')
                        ->icon('heroicon-o-archive-box')
                        ->url('/admin/inventory'),
                ]),
            NavigationGroup::make('Reports')
                ->items([
                    NavigationItem::make('Sales Reports')
                        ->icon('heroicon-o-chart-bar')
                        ->url('/admin/reports/sales'),
                ]),
        ];
    }

    private static function buildCashierNavigation(): array
    {
        return [
            NavigationGroup::make('Sales & Orders')
                ->items([
                    NavigationItem::make('Orders')
                        ->icon('heroicon-o-shopping-bag')
                        ->url('/admin/orders'),
                    NavigationItem::make('Payments')
                        ->icon('heroicon-o-credit-card')
                        ->url('/admin/payments'),
                ]),
            NavigationGroup::make('Customers')
                ->items([
                    NavigationItem::make('Members')
                        ->icon('heroicon-o-user-group')
                        ->url('/admin/members'),
                    NavigationItem::make('Tables')
                        ->icon('heroicon-o-table-cells')
                        ->url('/admin/tables'),
                ]),
        ];
    }
}