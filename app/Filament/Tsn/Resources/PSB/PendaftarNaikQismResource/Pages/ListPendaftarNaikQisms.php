<?php

namespace App\Filament\Tsn\Resources\PSB\PendaftarNaikQismResource\Pages;

use App\Filament\Tsn\Resources\PSB\PendaftarNaikQismResource;
use App\Filament\Tsn\Resources\PSB\PendaftarNaikQismResource\Widgets\ListPendaftarNaikQism;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;

class ListPendaftarNaikQisms extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = PendaftarNaikQismResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ListPendaftarNaikQism::class,
        ];
    }
}
