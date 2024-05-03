<?php

namespace App\Filament\Tsn\Resources\Imtihan\PerkembanganAnakDidikResource\Pages;

use App\Filament\Tsn\Resources\Imtihan\PerkembanganAnakDidikResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPerkembanganAnakDidik extends ViewRecord
{
    protected static string $resource = PerkembanganAnakDidikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }
}
