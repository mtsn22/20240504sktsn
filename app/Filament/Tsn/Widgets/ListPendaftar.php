<?php

namespace App\Filament\Tsn\Widgets;

use App\Filament\Tsn\Resources\PSB\PendaftarNaikQismResource;
use App\Filament\Tsn\Resources\PSB\PendaftarSantriBaruResource;
use App\Models\Santri;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListPendaftar extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return auth()->user()->id==0;
    }

    protected function getStats(): array
    {
        $baruputra = Santri::query()
            ->where(fn (Builder $query) => $query
                ->whereHas('statussantri', function ($query) {
                    $query->where('status', 'calon');
                })
                ->whereHas('kelasSantris', function ($query) {
                    $query->whereIn('qism_id', Auth::user()->mudirqism);
                })
                ->whereHas('kelasSantris.qism_detail', function ($query) {
                    $query->where('jeniskelamin', 'Putra');
                }))
            ->count();

        $naikqismputra = Santri::query()
            ->where(fn (Builder $query) => $query
                ->whereIn('qism_id', Auth::user()->mudirqism)
                ->where('daftarnaikqism', 'Mendaftar')
                ->where('jenispendaftar', null)
                ->whereHas('kelasSantris.qism_detail', function ($query) {
                    $query->where('jeniskelamin', 'Putra');
                }))
            ->count();

        $baruputri = Santri::query()
            ->where(fn (Builder $query) => $query
                ->whereHas('statussantri', function ($query) {
                    $query->where('status', 'calon');
                })
                ->whereHas('kelasSantris', function ($query) {
                    $query->whereIn('qism_id', Auth::user()->mudirqism);
                })
                ->whereHas('kelasSantris.qism_detail', function ($query) {
                    $query->where('jeniskelamin', 'Putri');
                }))
            ->count();

        $naikqismputri = Santri::query()
            ->where(fn (Builder $query) => $query
                ->whereIn('qism_id', Auth::user()->mudirqism)
                ->where('daftarnaikqism', 'Mendaftar')
                ->where('jenispendaftar', null)
                ->whereHas('kelasSantris.qism_detail', function ($query) {
                    $query->where('jeniskelamin', 'Putri');
                }))
            ->count();

        // dd($baru, $naikqism);

        return [

            Stat::make(
                label: 'Pendaftar Santri Baru Putra',
                value: Santri::query()
                    ->where(fn (Builder $query) => $query
                        ->whereHas('statussantri', function ($query) {
                            $query->where('status', 'calon');
                        })
                        ->whereHas('kelasSantris', function ($query) {
                            $query->whereIn('qism_id', Auth::user()->mudirqism);
                        })
                        ->whereHas('kelasSantris.qism_detail', function ($query) {
                            $query->where('jeniskelamin', 'Putra');
                        }))
                    ->count(),
            )->url(PendaftarSantriBaruResource::getUrl()),

            Stat::make(
                label: 'Pendaftar Naik Qism Putra',
                value: Santri::query()
                    ->where(fn (Builder $query) => $query
                        ->whereIn('qism_id', Auth::user()->mudirqism)
                        ->where('daftarnaikqism', 'Mendaftar')
                        ->where('jenispendaftar', null)
                        ->whereHas('qism_detail', function ($query) {
                            $query->where('jeniskelamin', 'Putra');
                        }))
                    ->count(),
            )->url(PendaftarNaikQismResource::getUrl()),

            Stat::make(
                label: 'Total Pendaftar Putra',
                value: $baruputra + $naikqismputra
            ),



            Stat::make(
                label: 'Pendaftar Santri Baru Putri',
                value: Santri::query()
                    ->where(fn (Builder $query) => $query
                        ->whereHas('statussantri', function ($query) {
                            $query->where('status', 'calon');
                        })
                        ->whereHas('kelasSantris', function ($query) {
                            $query->whereIn('qism_id', Auth::user()->mudirqism);
                        })
                        ->whereHas('kelasSantris.qism_detail', function ($query) {
                            $query->where('jeniskelamin', 'Putri');
                        }))
                    ->count(),
            )->url(PendaftarSantriBaruResource::getUrl()),

            Stat::make(
                label: 'Pendaftar Naik Qism Putri',
                value: Santri::query()
                    ->where(fn (Builder $query) => $query
                        ->whereIn('qism_id', Auth::user()->mudirqism)
                        ->where('daftarnaikqism', 'Mendaftar')
                        ->where('jenispendaftar', null)
                        ->whereHas('qism_detail', function ($query) {
                            $query->where('jeniskelamin', 'Putri');
                        }))
                    ->count(),
            )->url(PendaftarNaikQismResource::getUrl()),

            Stat::make(
                label: 'Total Pendaftar Putri',
                value: $baruputri + $naikqismputri
            ),


        ];
    }
}
