<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

class PosWorkflow extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    
    protected static string $view = 'filament.pages.pos-workflow';
    
    protected static ?string $navigationGroup = 'Sales & Orders';
    
    protected static ?int $navigationSort = 0;
    
    protected static ?string $title = 'Point of Sale';

    public $selectedProducts = [];
    public $selectedTable = null;
    public $orderTotal = 0;

    public static function canAccess(): bool
    {
        return Auth::user()->hasAnyRole(['owner', 'manager', 'cashier']);
    }

    public function mount(): void
    {
        $this->selectedProducts = [];
        $this->calculateTotal();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('newOrder')
                ->label('New Order')
                ->icon('heroicon-o-plus')
                ->action('clearOrder'),
            Action::make('processPayment')
                ->label('Process Payment')
                ->icon('heroicon-o-credit-card')
                ->color('success')
                ->disabled(fn () => empty($this->selectedProducts))
                ->action('processPayment'),
        ];
    }

    public function addProduct($productId): void
    {
        $product = Product::find($productId);
        
        if (!$product) {
            return;
        }

        $key = $productId;
        
        if (isset($this->selectedProducts[$key])) {
            $this->selectedProducts[$key]['quantity']++;
        } else {
            $this->selectedProducts[$key] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
            ];
        }

        $this->calculateTotal();
    }

    public function removeProduct($key): void
    {
        unset($this->selectedProducts[$key]);
        $this->calculateTotal();
    }

    public function updateQuantity($key, $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeProduct($key);
            return;
        }

        $this->selectedProducts[$key]['quantity'] = $quantity;
        $this->calculateTotal();
    }

    public function calculateTotal(): void
    {
        $this->orderTotal = collect($this->selectedProducts)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    public function clearOrder(): void
    {
        $this->selectedProducts = [];
        $this->selectedTable = null;
        $this->orderTotal = 0;
    }

    public function processPayment(): void
    {
        if (empty($this->selectedProducts)) {
            return;
        }

        // Create order logic would go here
        $this->redirect('/admin/orders/create');
    }
}