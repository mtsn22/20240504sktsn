<?php

namespace App\Filament\Admin\Resources\DataNilaiResource\Pages;

use App\Filament\Admin\Resources\DataNilaiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDataNilai extends EditRecord
{
    protected static string $resource = DataNilaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
