<?php

namespace App\Filament\Tsn\Resources\PSB\PendaftarNaikQismResource\Widgets;

use App\Filament\Tsn\Resources\PSB\PendaftarNaikQismResource\Pages\ListPendaftarNaikQisms;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ListPendaftarNaikQism extends BaseWidget
{
    use InteractsWithPageTable;

    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected function getTablePage(): string
    {
        return ListPendaftarNaikQisms::class;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Pendaftar Naik Qism Putra', $this->getPageTableQuery()
            ->whereIn('qism_id', Auth::user()->mudirqism)
            ->where('daftarnaikqism', 'Mendaftar')
            ->where('jenispendaftar', 'naikqism')
            ->whereHas('kelasSantris.qism_detail', function ($query) {
                $query->where('jeniskelamin', 'Putra');
            })
        ->count()),

        Stat::make('Pendaftar Naik Qism Putri', $this->getPageTableQuery()
            ->whereIn('qism_id', Auth::user()->mudirqism)
            ->where('daftarnaikqism', 'Mendaftar')
            ->where('jenispendaftar', 'naikqism')
            ->whereHas('kelasSantris.qism_detail', function ($query) {
                $query->where('jeniskelamin', 'Putri');
            })
        ->count()),
        ];
    }
}
