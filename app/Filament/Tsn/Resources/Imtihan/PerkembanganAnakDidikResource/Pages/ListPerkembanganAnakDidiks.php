<?php

namespace App\Filament\Tsn\Resources\Imtihan\PerkembanganAnakDidikResource\Pages;

use App\Filament\Tsn\Resources\Imtihan\PerkembanganAnakDidikResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPerkembanganAnakDidiks extends ListRecords
{
    protected static string $resource = PerkembanganAnakDidikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
