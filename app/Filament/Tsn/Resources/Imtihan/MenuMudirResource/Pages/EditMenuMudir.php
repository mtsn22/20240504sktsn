<?php

namespace App\Filament\Tsn\Resources\Imtihan\MenuMudirResource\Pages;

use App\Filament\Tsn\Resources\Imtihan\MenuMudirResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMenuMudir extends EditRecord
{
    protected static string $resource = MenuMudirResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            // Actions\DeleteAction::make(),
        ];
    }
}
