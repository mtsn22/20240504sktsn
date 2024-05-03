<?php

namespace App\Filament\Walisantri\Resources\DataNilaiResource\Pages;

use App\Filament\Walisantri\Resources\DataNilaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDataNilai extends ViewRecord
{
    protected static string $resource = DataNilaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }
}
