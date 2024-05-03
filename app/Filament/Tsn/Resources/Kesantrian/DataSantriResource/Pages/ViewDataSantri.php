<?php

namespace App\Filament\Tsn\Resources\Kesantrian\DataSantriResource\Pages;

use App\Filament\Tsn\Resources\Kesantrian\DataSantriResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDataSantri extends ViewRecord
{
    protected static string $resource = DataSantriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }
}
