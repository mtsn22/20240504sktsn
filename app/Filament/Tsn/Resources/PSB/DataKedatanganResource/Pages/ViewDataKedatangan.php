<?php

namespace App\Filament\Tsn\Resources\PSB\DataKedatanganResource\Pages;

use App\Filament\Tsn\Resources\PSB\DataKedatanganResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDataKedatangan extends ViewRecord
{
    protected static string $resource = DataKedatanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }
}
