<?php

namespace App\Filament\Admin\Resources\TahunBerjalanResource\Pages;

use App\Filament\Admin\Resources\TahunBerjalanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTahunBerjalan extends EditRecord
{
    protected static string $resource = TahunBerjalanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
