<?php

namespace App\Filament\Admin\Resources\AdminDataSantriResource\Pages;

use App\Filament\Admin\Resources\AdminDataSantriResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdminDataSantris extends ListRecords
{
    protected static string $resource = AdminDataSantriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
