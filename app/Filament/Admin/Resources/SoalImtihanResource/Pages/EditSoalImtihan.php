<?php

namespace App\Filament\Admin\Resources\SoalImtihanResource\Pages;

use App\Filament\Admin\Resources\SoalImtihanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSoalImtihan extends EditRecord
{
    protected static string $resource = SoalImtihanResource::class;

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
