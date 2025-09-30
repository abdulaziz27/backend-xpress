<?php

namespace App\Filament\Pages;

use App\Models\Product;
use App\Models\InventoryMovement;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

class InventoryDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    
    protected static string $view = 'filament.pages.inventory-dashboard';
    
    protected static ?string $navigationGroup = 'Inventory';
    
    protected static ?int $navigationSort = 0;
    
    protected static ?string $title = 'Inventory Overview';

    public static function canAccess(): bool
    {
        return Auth::user()->hasAnyRole(['owner', 'manager']) && 
               Auth::user()->store->subscription->plan->hasFeature('inventory_tracking');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('stockAdjustment')
                ->label('Stock Adjustment')
                ->icon('heroicon-o-adjustments-horizontal')
                ->url('/admin/inventory-movements/create'),
            Action::make('lowStockReport')
                ->label('Low Stock Report')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('warning')
                ->action('generateLowStockReport'),
        ];
    }

    public function generateLowStockReport(): void
    {
        $this->redirect('/admin/reports/low-stock');
    }

    public function getLowStockProducts()
    {
        return Product::where('store_id', auth()->user()->store_id)
            ->where('track_inventory', true)
            ->whereColumn('stock_quantity', '<=', 'min_stock_level')
            ->get();
    }

    public function getRecentMovements()
    {
        return InventoryMovement::where('store_id', auth()->user()->store_id)
            ->with('product')
            ->latest()
            ->limit(10)
            ->get();
    }

    public function getInventoryStats()
    {
        $storeId = auth()->user()->store_id;
        
        return [
            'total_products' => Product::where('store_id', $storeId)->where('track_inventory', true)->count(),
            'low_stock_count' => Product::where('store_id', $storeId)
                ->where('track_inventory', true)
                ->whereColumn('stock_quantity', '<=', 'min_stock_level')
                ->count(),
            'out_of_stock_count' => Product::where('store_id', $storeId)
                ->where('track_inventory', true)
                ->where('stock_quantity', 0)
                ->count(),
            'total_value' => Product::where('store_id', $storeId)
                ->where('track_inventory', true)
                ->selectRaw('SUM(stock_quantity * cost_price) as total')
                ->value('total') ?? 0,
        ];
    }
}