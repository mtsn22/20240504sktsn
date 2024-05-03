<?php

namespace App\Filament\Admin\Resources\AdminKelasSantriResource\Pages;

use App\Filament\Admin\Resources\AdminKelasSantriResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAdminKelasSantri extends ViewRecord
{
    protected static string $resource = AdminKelasSantriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
