<?php

namespace App\Filament\Admin\Resources\AdminKelasSantriResource\Pages;

use App\Filament\Admin\Resources\AdminKelasSantriResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdminKelasSantris extends ListRecords
{
    protected static string $resource = AdminKelasSantriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
