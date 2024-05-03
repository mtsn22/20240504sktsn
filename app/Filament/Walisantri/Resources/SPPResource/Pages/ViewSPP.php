<?php

namespace App\Filament\Walisantri\Resources\SPPResource\Pages;

use App\Filament\Walisantri\Resources\SPPResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSPP extends ViewRecord
{
    protected static string $resource = SPPResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }
}
