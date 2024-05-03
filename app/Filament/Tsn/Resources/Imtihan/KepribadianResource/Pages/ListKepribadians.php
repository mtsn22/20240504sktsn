<?php

namespace App\Filament\Tsn\Resources\Imtihan\KepribadianResource\Pages;

use App\Filament\Tsn\Resources\Imtihan\KepribadianResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKepribadians extends ListRecords
{
    protected static string $resource = KepribadianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
