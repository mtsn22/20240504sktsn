<?php

namespace App\Filament\Tsn\Resources\Imtihan\NilaiTulisLisanResource\Pages;

use App\Filament\Tsn\Resources\Imtihan\NilaiTulisLisanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewNilaiTulisLisan extends ViewRecord
{
    protected static string $resource = NilaiTulisLisanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }
}
