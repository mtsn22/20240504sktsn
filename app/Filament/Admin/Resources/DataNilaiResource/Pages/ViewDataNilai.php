<?php

namespace App\Filament\Admin\Resources\DataNilaiResource\Pages;

use App\Filament\Admin\Resources\DataNilaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDataNilai extends ViewRecord
{
    protected static string $resource = DataNilaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
