<?php

namespace App\Filament\Tsn\Resources\Imtihan\NilaiTulisLisanResource\Pages;

use App\Filament\Tsn\Resources\Imtihan\NilaiTulisLisanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNilaiTulisLisans extends ListRecords
{
    protected static string $resource = NilaiTulisLisanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
