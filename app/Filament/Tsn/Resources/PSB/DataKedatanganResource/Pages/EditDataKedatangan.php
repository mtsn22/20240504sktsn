<?php

namespace App\Filament\Tsn\Resources\PSB\DataKedatanganResource\Pages;

use App\Filament\Tsn\Resources\PSB\DataKedatanganResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDataKedatangan extends EditRecord
{
    protected static string $resource = DataKedatanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\ViewAction::make(),
            // Actions\DeleteAction::make(),
        ];
    }
}
