<?php

namespace App\Filament\SystemAdmin\Resources\GlobalUserResource\Pages;

use App\Filament\SystemAdmin\Resources\GlobalUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGlobalUsers extends ListRecords
{
    protected static string $resource = GlobalUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}