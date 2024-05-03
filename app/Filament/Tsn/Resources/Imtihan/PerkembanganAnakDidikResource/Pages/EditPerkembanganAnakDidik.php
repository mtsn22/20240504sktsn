<?php

namespace App\Filament\Tsn\Resources\Imtihan\PerkembanganAnakDidikResource\Pages;

use App\Filament\Tsn\Resources\Imtihan\PerkembanganAnakDidikResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPerkembanganAnakDidik extends EditRecord
{
    protected static string $resource = PerkembanganAnakDidikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            // Actions\DeleteAction::make(),
        ];
    }
}
