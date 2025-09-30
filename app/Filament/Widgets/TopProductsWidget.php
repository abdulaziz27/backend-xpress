<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class TopProductsWidget extends BaseWidget
{
    protected static ?string $heading = 'Top Products (This Month)';
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                OrderItem::query()
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->where('orders.store_id', auth()->user()->store_id)
                    ->where('orders.status', 'completed')
                    ->whereMonth('orders.created_at', Carbon::now()->month)
                    ->selectRaw('
                        products.name,
                        products.price,
                        SUM(order_items.quantity) as total_quantity,
                        SUM(order_items.quantity * order_items.unit_price) as total_revenue
                    ')
                    ->groupBy('products.id', 'products.name', 'products.price')
                    ->orderByDesc('total_quantity')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Product')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Unit Price')
                    ->money('USD'),
                Tables\Columns\TextColumn::make('total_quantity')
                    ->label('Sold')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Revenue')
                    ->money('USD')
                    ->sortable(),
            ]);
    }
}