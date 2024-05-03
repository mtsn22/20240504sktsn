<?php

namespace App\Filament\Admin\Resources\DataNilaiResource\Pages;

use App\Filament\Admin\Resources\DataNilaiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDataNilai extends CreateRecord
{
    protected static string $resource = DataNilaiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
