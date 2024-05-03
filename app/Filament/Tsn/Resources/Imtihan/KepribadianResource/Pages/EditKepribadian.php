<?php

namespace App\Filament\Tsn\Resources\Imtihan\KepribadianResource\Pages;

use App\Filament\Tsn\Resources\Imtihan\KepribadianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKepribadian extends EditRecord
{
    protected static string $resource = KepribadianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            // Actions\DeleteAction::make(),
        ];
    }
}
