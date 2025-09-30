<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Automatically assign current store and user
        $data['store_id'] = auth()->user()->store_id;
        $data['user_id'] = auth()->id();
        return $data;
    }
}