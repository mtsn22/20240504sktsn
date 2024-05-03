<?php

namespace App\Filament\Tsn\Resources\Imtihan\NilaiHafalanResource\Pages;

use App\Filament\Tsn\Resources\Imtihan\NilaiHafalanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNilaiHafalan extends EditRecord
{
    protected static string $resource = NilaiHafalanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            // Actions\DeleteAction::make(),
        ];
    }
}
