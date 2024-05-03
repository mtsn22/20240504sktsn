<?php

namespace App\Filament\Admin\Resources\TsnuniqueResource\Pages;

use App\Filament\Admin\Resources\TsnuniqueResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTsnunique extends ViewRecord
{
    protected static string $resource = TsnuniqueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
