<?php

namespace App\Filament\SystemAdmin\Resources\StoreResource\Pages;

use App\Filament\SystemAdmin\Resources\StoreResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStore extends ViewRecord
{
    protected static string $resource = StoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}