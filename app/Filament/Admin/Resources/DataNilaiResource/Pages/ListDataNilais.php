<?php

namespace App\Filament\Admin\Resources\DataNilaiResource\Pages;

use App\Filament\Admin\Resources\DataNilaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDataNilais extends ListRecords
{
    protected static string $resource = DataNilaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
