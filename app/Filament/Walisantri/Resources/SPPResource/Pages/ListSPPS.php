<?php

namespace App\Filament\Walisantri\Resources\SPPResource\Pages;

use App\Filament\Walisantri\Resources\SPPResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSPPS extends ListRecords
{
    protected static string $resource = SPPResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
