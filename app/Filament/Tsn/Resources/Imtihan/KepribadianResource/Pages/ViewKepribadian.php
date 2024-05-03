<?php

namespace App\Filament\Tsn\Resources\Imtihan\KepribadianResource\Pages;

use App\Filament\Tsn\Resources\Imtihan\KepribadianResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewKepribadian extends ViewRecord
{
    protected static string $resource = KepribadianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }
}
