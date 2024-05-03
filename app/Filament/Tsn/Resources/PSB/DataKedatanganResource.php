<?php

namespace App\Filament\Tsn\Resources\PSB;

use App\Filament\Tsn\Resources\PSB\DataKedatanganResource\Pages;
use App\Filament\Tsn\Resources\PSB\DataKedatanganResource\RelationManagers;
use App\Models\PSB\DataKedatangan;
use App\Models\TahunBerjalan;
use App\Models\Walisantri;
use Carbon\Carbon as CarbonCarbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Filament\Tables\Grouping\Group;
use Illuminate\Support\Carbon;

class DataKedatanganResource extends Resource
{
    protected static ?string $navigationGroup = 'PSB';

    protected static ?int $navigationSort = 01030;

    protected static ?string $modelLabel = 'Data Kedatangan';

    protected static ?string $navigationLabel = 'Data Kedatangan';

    protected static ?string $pluralModelLabel = 'Data Kedatangan';

    protected static ?string $model = Walisantri::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->defaultPaginationPageOption('all')
            ->columns([
                TextColumn::make('ak_nama_lengkap')
                    ->label('Nama')
                    ->searchable(isIndividual: true, isGlobal: false),

                TextColumn::make('santris.nama_lengkap')
                    ->label('Nama Santri')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->searchable(isIndividual: true, isGlobal: false),

                TextColumn::make('santris.kelassantri.qism_detail.abbr_qism_detail')
                    ->label('Qism')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->searchable(isIndividual: true, isGlobal: false),

                TextColumn::make('al_ak_kabupaten.kabupaten')
                    ->label('Kota Asal')
                    ->searchable(isIndividual: true, isGlobal: false),

                TextColumn::make('al_ak_provinsi.provinsi')
                    ->label('Provinsi')
                    ->searchable(isIndividual: true, isGlobal: false),

                TextColumn::make('putra')
                    ->label('Jumlah Tamu Putra')
                    ->summarize(Sum::make()->label('Total Putra'))
                    ->searchable(isIndividual: true, isGlobal: false),

                TextColumn::make('putri')
                    ->label('Jumlah Tamu Putri')
                    ->summarize(Sum::make()->label('Total Putri'))
                    ->searchable(isIndividual: true, isGlobal: false),

                TextColumn::make('tanggal_datang')
                    ->label('Tanggal Datang')
                    ->date()
                    ->sortable()
                    // ->summarize(Range::make()->label('')->minimalDateTimeDifference())
                    ->summarize(Count::make()->label('KK datang'))
                    ->searchable(isIndividual: true, isGlobal: false),

                TextColumn::make('waktu_datang')
                    ->label('Waktu Datang')
                    ->searchable(isIndividual: true, isGlobal: false),

                TextColumn::make('tanggal_kembali')
                    ->label('Tanggal Kembali')
                    ->date()
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false),

                TextColumn::make('waktu_kembali')
                    ->label('Waktu Kembali')
                    ->searchable(isIndividual: true, isGlobal: false),

                TextColumn::make('jumlah_hari')
                    ->label('Jumlah Hari')
                    ->searchable(isIndividual: true, isGlobal: false),

                TextColumn::make('menginap')
                    ->label('Status Menginap')
                    // ->summarize(Count::make()->label('Total Menginap/Tidak Menginap'))
                    ->searchable(isIndividual: true, isGlobal: false),

                TextColumn::make('informasi_lain')
                    ->label('Informasi Lain')
                    ->searchable(isIndividual: true, isGlobal: false),
            ])
            ->groups([
            Group::make('tanggal_datang')
                ->date(),
        ])
            ->defaultGroup('tanggal_datang')
            ->defaultSort('waktu_datang')
            ->filters([

                SelectFilter::make('menginap')
                    ->label('Status Menginap')
                    ->options([
                        'Tidak Menginap' => 'Tidak Menginap',
                        'Menginap' => 'Menginap',
                    ]),

            ], layout: FiltersLayout::AboveContent)
            ->actions([
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDataKedatangans::route('/'),
            'create' => Pages\CreateDataKedatangan::route('/create'),
            'view' => Pages\ViewDataKedatangan::route('/{record}'),
            'edit' => Pages\EditDataKedatangan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('user', function ($query) {
                $query->where('is_active', 1);
            })->where('tanggal_datang', '!=', null);
    }
}
