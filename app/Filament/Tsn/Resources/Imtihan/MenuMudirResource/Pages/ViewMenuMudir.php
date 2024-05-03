<?php

namespace App\Filament\Tsn\Resources\Imtihan\MenuMudirResource\Pages;

use App\Filament\Tsn\Resources\Imtihan\MenuMudirResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMenuMudir extends ViewRecord
{
    protected static string $resource = MenuMudirResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }
}
