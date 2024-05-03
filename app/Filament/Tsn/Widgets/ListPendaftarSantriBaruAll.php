<?php

namespace App\Filament\Tsn\Widgets;

use App\Filament\Tsn\Resources\PSB\PendaftarSantriBaruResource;
use App\Models\Santri;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListPendaftarSantriBaruAll extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->id==0;
    }

    protected function getStats(): array
    {
        return [
            Stat::make(
                label: 'Total Pendaftar Santri Baru Semua Qism',
                value: Santri::query()
                    ->where(fn (Builder $query) => $query
                    ->whereHas('statussantri', function ($query) {
                        $query->where('status', 'calon');
                    })
                    ->whereHas('kelasSantris', function ($query) {
                        $query->whereIn('qism_detail_id', ["1","2","3","4","5","6","7","8","9"]);
                    }))
                    ->count(),
            )
            ->url(PendaftarSantriBaruResource::getUrl()),

            Stat::make(
                label: 'Total Pendaftar Naik Qism Semua Qism',
                value: Santri::query()
                    ->where(fn (Builder $query) => $query
                    ->where('daftarnaikqism','Mendaftar')
                    ->whereIn('qism_detail_id', ["1","2","3","4","5","6","7","8","9"]))
                    ->count(),
            ),

            Stat::make(
                label: 'Total Pendaftar Santri Baru: Qism TAPa',
                value: Santri::query()
                    ->where(fn (Builder $query) => $query
                    ->whereHas('statussantri', function ($query) {
                        $query->where('status', 'calon');
                    })
                    ->whereHas('kelasSantris', function ($query) {
                        $query->whereIn('qism_detail_id', ["1"]);
                    }))
                    ->count(),
            ),

            Stat::make(
                label: 'Total Pendaftar Naik Qism ke Qism TAPa',
                value: Santri::query()
                    ->where(fn (Builder $query) => $query
                    ->where('daftarnaikqism','Mendaftar')
                    ->where('qism_detail_id','1'))
                    ->count(),
            ),

            Stat::make(
                label: 'Total Pendaftar Santri Baru: Qism TAPi',
                value: Santri::query()
                    ->where(fn (Builder $query) => $query
                    ->whereHas('statussantri', function ($query) {
                        $query->where('status', 'calon');
                    })
                    ->whereHas('kelasSantris', function ($query) {
                        $query->whereIn('qism_detail_id', ["2"]);
                    }))
                    ->count(),
            ),

            Stat::make(
                label: 'Total Pendaftar Naik Qism ke Qism TAPi',
                value: Santri::query()
                    ->where(fn (Builder $query) => $query
                    ->where('daftarnaikqism','Mendaftar')
                    ->where('qism_detail_id','1'))
                    ->count(),
            ),

            Stat::make(
                label: 'Total Pendaftar Santri Baru: Qism PTPa',
                value: Santri::query()
                    ->where(fn (Builder $query) => $query
                    ->whereHas('statussantri', function ($query) {
                        $query->where('status', 'calon');
                    })
                    ->whereHas('kelasSantris', function ($query) {
                        $query->whereIn('qism_detail_id', ["3"]);
                    }))
                    ->count(),
            ),

            Stat::make(
                label: 'Total Pendaftar Naik Qism ke Qism PTPa',
                value: Santri::query()
                    ->where(fn (Builder $query) => $query
                    ->where('daftarnaikqism','Mendaftar')
                    ->where('qism_detail_id','3'))
                    ->count(),
            ),

            Stat::make(
                label: 'Total Pendaftar Santri Baru: Qism PTPi',
                value: Santri::query()
                    ->where(fn (Builder $query) => $query
                    ->whereHas('statussantri', function ($query) {
                        $query->where('status', 'calon');
                    })
                    ->whereHas('kelasSantris', function ($query) {
                        $query->whereIn('qism_detail_id', ["4"]);
                    }))
                    ->count(),
            ),

            Stat::make(
                label: 'Total Pendaftar Naik Qism ke Qism PTPi',
                value: Santri::query()
                    ->where(fn (Builder $query) => $query
                    ->where('daftarnaikqism','Mendaftar')
                    ->where('qism_detail_id','4'))
                    ->count(),
            ),

            Stat::make(
                label: 'Total Pendaftar Santri Baru: Qism TQPa',
                value: Santri::query()
                    ->where(fn (Builder $query) => $query
                    ->whereHas('statussantri', function ($query) {
                        $query->where('status', 'calon');
                    })
                    ->whereHas('kelasSantris', function ($query) {
                        $query->whereIn('qism_detail_id', ["5"]);
                    }))
                    ->count(),
            ),

            Stat::make(
                label: 'Total Pendaftar Naik Qism ke Qism TQPa',
                value: Santri::query()
                    ->where(fn (Builder $query) => $query
                    ->where('daftarnaikqism','Mendaftar')
                    ->where('qism_detail_id','5'))
                    ->count(),
            ),

            Stat::make(
                label: 'Total Pendaftar Santri Baru: Qism TQPi',
                value: Santri::query()
                    ->where(fn (Builder $query) => $query
                    ->whereHas('statussantri', function ($query) {
                        $query->where('status', 'calon');
                    })
                    ->whereHas('kelasSantris', function ($query) {
                        $query->whereIn('qism_detail_id', ["6"]);
                    }))
                    ->count(),
            ),

            Stat::make(
                label: 'Total Pendaftar Naik Qism ke Qism TQPi',
                value: Santri::query()
                    ->where(fn (Builder $query) => $query
                    ->where('daftarnaikqism','Mendaftar')
                    ->where('qism_detail_id','6'))
                    ->count(),
            ),

            Stat::make(
                label: 'Total Pendaftar Santri Baru: Qism IDD',
                value: Santri::query()
                    ->where(fn (Builder $query) => $query
                    ->whereHas('statussantri', function ($query) {
                        $query->where('status', 'calon');
                    })
                    ->whereHas('kelasSantris', function ($query) {
                        $query->whereIn('qism_detail_id', ["7"]);
                    }))
                    ->count(),
            ),

            Stat::make(
                label: 'Total Pendaftar Naik Qism ke Qism IDD',
                value: Santri::query()
                    ->where(fn (Builder $query) => $query
                    ->where('daftarnaikqism','Mendaftar')
                    ->where('qism_detail_id','7'))
                    ->count(),
            ),

            Stat::make(
                label: 'Total Pendaftar Santri Baru: Qism MTW',
                value: Santri::query()
                    ->where(fn (Builder $query) => $query
                    ->whereHas('statussantri', function ($query) {
                        $query->where('status', 'calon');
                    })
                    ->whereHas('kelasSantris', function ($query) {
                        $query->whereIn('qism_detail_id', ["8"]);
                    }))
                    ->count(),
            ),

            Stat::make(
                label: 'Total Pendaftar Naik Qism ke Qism MTW',
                value: Santri::query()
                    ->where(fn (Builder $query) => $query
                    ->where('daftarnaikqism','Mendaftar')
                    ->where('qism_detail_id','8'))
                    ->count(),
            ),

            Stat::make(
                label: 'Total Pendaftar Santri Baru: Qism TN',
                value: Santri::query()
                    ->where(fn (Builder $query) => $query
                    ->whereHas('statussantri', function ($query) {
                        $query->where('status', 'calon');
                    })
                    ->whereHas('kelasSantris', function ($query) {
                        $query->whereIn('qism_detail_id', ["9"]);
                    }))
                    ->count(),
            ),

            Stat::make(
                label: 'Total Pendaftar Naik Qism ke Qism TN',
                value: Santri::query()
                    ->where(fn (Builder $query) => $query
                    ->where('daftarnaikqism','Mendaftar')
                    ->where('qism_detail_id','9'))
                    ->count(),
            ),
        ];
    }
}
