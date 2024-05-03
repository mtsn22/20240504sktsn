<?php

namespace App\Filament\Admin\Resources\AdminDataSantriResource\Pages;

use App\Filament\Admin\Resources\AdminDataSantriResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAdminDataSantri extends ViewRecord
{
    protected static string $resource = AdminDataSantriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
