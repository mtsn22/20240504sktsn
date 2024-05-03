<?php

namespace App\Filament\Tsn\Resources\Imtihan\NilaiHafalanResource\Pages;

use App\Filament\Tsn\Resources\Imtihan\NilaiHafalanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNilaiHafalans extends ListRecords
{
    protected static string $resource = NilaiHafalanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
