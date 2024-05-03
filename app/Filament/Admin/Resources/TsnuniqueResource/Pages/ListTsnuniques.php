<?php

namespace App\Filament\Admin\Resources\TsnuniqueResource\Pages;

use App\Filament\Admin\Resources\TsnuniqueResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTsnuniques extends ListRecords
{
    protected static string $resource = TsnuniqueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
