<?php

namespace App\Filament\Admin\Resources\AdminKelasSantriResource\Pages;

use App\Filament\Admin\Resources\AdminKelasSantriResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdminKelasSantri extends EditRecord
{
    protected static string $resource = AdminKelasSantriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
