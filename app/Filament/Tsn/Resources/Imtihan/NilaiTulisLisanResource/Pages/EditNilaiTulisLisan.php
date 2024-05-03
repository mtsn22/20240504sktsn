<?php

namespace App\Filament\Tsn\Resources\Imtihan\NilaiTulisLisanResource\Pages;

use App\Filament\Tsn\Resources\Imtihan\NilaiTulisLisanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNilaiTulisLisan extends EditRecord
{
    protected static string $resource = NilaiTulisLisanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            // Actions\DeleteAction::make(),
        ];
    }
}
