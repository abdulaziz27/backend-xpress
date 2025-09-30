<?php

namespace App\Filament\SystemAdmin\Resources\GlobalUserResource\Pages;

use App\Filament\SystemAdmin\Resources\GlobalUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGlobalUser extends EditRecord
{
    protected static string $resource = GlobalUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}