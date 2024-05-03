<?php

namespace App\Filament\Admin\Resources\TahunBerjalanResource\Pages;

use App\Filament\Admin\Resources\TahunBerjalanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTahunBerjalans extends ListRecords
{
    protected static string $resource = TahunBerjalanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
