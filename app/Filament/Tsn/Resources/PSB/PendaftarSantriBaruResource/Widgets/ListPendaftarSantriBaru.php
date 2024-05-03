<?php

namespace App\Filament\Tsn\Resources\PSB\PendaftarSantriBaruResource\Widgets;

use App\Filament\Tsn\Resources\PSB\PendaftarSantriBaruResource\Pages\ListPendaftarSantriBarus;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ListPendaftarSantriBaru extends BaseWidget
{
    use InteractsWithPageTable;

    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected function getTablePage(): string
    {
        return ListPendaftarSantriBarus::class;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Pendaftar Santri Baru Putra', $this->getPageTableQuery()
                ->whereHas('statussantri', function ($query) {
                    $query->where('status', 'calon');
                })
                ->whereHas('kelasSantris', function ($query) {
                    $query->whereIn('qism_id', Auth::user()->mudirqism);
                })
                ->whereHas('kelasSantris.qism_detail', function ($query) {
                    $query->where('jeniskelamin', 'Putra');
                })
                ->count()),

            Stat::make('Pendaftar Santri Baru Putri', $this->getPageTableQuery()
                ->whereHas('statussantri', function ($query) {
                    $query->where('status', 'calon');
                })
                ->whereHas('kelasSantris', function ($query) {
                    $query->whereIn('qism_id', Auth::user()->mudirqism);
                })
                ->whereHas('kelasSantris.qism_detail', function ($query) {
                    $query->where('jeniskelamin', 'Putri');
                })
                ->count()),
        ];
    }
}
