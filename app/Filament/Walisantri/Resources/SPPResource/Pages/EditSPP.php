<?php

namespace App\Filament\Walisantri\Resources\SPPResource\Pages;

use App\Filament\Walisantri\Resources\SPPResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSPP extends EditRecord
{
    protected static string $resource = SPPResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\ViewAction::make(),
            // Actions\DeleteAction::make(),
        ];
    }
}
