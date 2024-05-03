<?php

namespace App\Filament\Tsn\Resources\PSB\UpdateSiapNaikQismResource\Pages;

use App\Filament\Tsn\Resources\PSB\UpdateSiapNaikQismResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUpdateSiapNaikQism extends ViewRecord
{
    protected static string $resource = UpdateSiapNaikQismResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
