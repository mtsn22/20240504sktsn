<?php

namespace App\Filament\Tsn\Resources\PSB\DataKeringananResource\Pages;

use App\Filament\Tsn\Resources\PSB\DataKeringananResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDataKeringanan extends ViewRecord
{
    protected static string $resource = DataKeringananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
