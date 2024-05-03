<?php

namespace App\Filament\Admin\Resources\AdminDataSantriResource\Pages;

use App\Filament\Admin\Resources\AdminDataSantriResource;
use App\Models\Santri;
use App\Models\Walisantri;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdminDataSantri extends EditRecord
{
    protected static string $resource = AdminDataSantriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $walisantri = Walisantri::where('id', $this->record->walisantri_id)->first();
        $walisantri->ws_emis4 = '1';
        $walisantri->save();

        $santri = Santri::where('id', $this->record->santri_id)->first();
        $santri->s_emis4 = '1';
        $santri->save();
    }

    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}
}
