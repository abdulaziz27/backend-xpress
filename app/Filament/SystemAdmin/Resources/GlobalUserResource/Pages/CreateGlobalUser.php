<?php

namespace App\Filament\SystemAdmin\Resources\GlobalUserResource\Pages;

use App\Filament\SystemAdmin\Resources\GlobalUserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGlobalUser extends CreateRecord
{
    protected static string $resource = GlobalUserResource::class;
}