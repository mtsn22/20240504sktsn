<?php

namespace App\Filament\Tsn\Resources\PSB\PendaftarSantriBaruResource\Pages;

use App\Filament\Tsn\Resources\PSB\PendaftarSantriBaruResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPendaftarSantriBaru extends EditRecord
{
    protected static string $resource = PendaftarSantriBaruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            // Actions\DeleteAction::make(),
        ];
    }
}
