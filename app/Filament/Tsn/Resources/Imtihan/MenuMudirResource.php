<?php

namespace App\Filament\Tsn\Resources\Imtihan;

use App\Filament\Tsn\Resources\Imtihan\MenuMudirResource\Pages;
use App\Filament\Tsn\Resources\Imtihan\MenuMudirResource\RelationManagers;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\MenuMudir;
use App\Models\Nilai;
use App\Models\Pengajar;
use App\Models\QismDetail;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class MenuMudirResource extends Resource
{

    public static function canViewAny(): bool
    {
        return auth()->user()->mudirqism !== null;
    }

    protected static ?string $navigationGroup = 'Menu Mudir';

    protected static ?int $navigationSort = 07010;

    protected static ?string $modelLabel = 'Semua Nilai';

    protected static ?string $navigationLabel = 'Semua Nilai';

    protected static ?string $pluralModelLabel = 'Semua Nilai';

    protected static ?string $model = Nilai::class;

    protected static ?string $navigationIcon = 'heroicon-s-user';

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
            ->heading('Data Semua Nilai per Qism')
            ->description('Data semua nilai per qism untuk pengecekan mudir qism')
            ->recordUrl(null)
            ->defaultPaginationPageOption('all')
            // ->striped()
            ->columns([

                TextColumn::make('file_nilai')
                    ->label('Link')
                    ->formatStateUsing(fn (string $state): string => __("Cek"))
                    ->icon('heroicon-s-pencil-square')
                    ->iconColor('success')
                    // ->circular()
                    ->alignCenter()
                    ->url(function (Model $record) {
                        if ($record->file_nilai !== null) {

                            return ($record->file_nilai);
                        }
                    })
                    ->badge()
                    ->color('info')
                    ->openUrlInNewTab(),

                CheckboxColumn::make('is_nilai_selesai')
                    ->label('Status')
                    ->alignCenter()
                    ->disabled()
                    ->sortable(),

                TextColumn::make('kelas.kelas')
                    ->label('Kelas')
                    ->sortable(),

                TextColumn::make('mapel.mapel')
                    ->label('Mapel')
                    ->sortable(),

                TextColumn::make('keterangan_nilai')
                    ->label('Keterangan')
                    ->sortable(),

                TextColumn::make('kode_soal')
                    ->label('Kode Soal')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('pengajar.nama')
                    ->label('Nama Pengajar')
                    ->sortable(),

            ])
            ->groups([
                Group::make('jenisSoal.jenis_soal')
                    ->titlePrefixedWithLabel(false)
            ])

            ->defaultGroup('jenisSoal.jenis_soal')
            ->defaultSort('kode_soal')
            ->filters([

                SelectFilter::make('qism_detail_id')
                    ->label('Qism')
                    ->multiple()
                    ->options(QismDetail::all()->pluck('abbr_qism_detail', 'id'))
                    ->visible(auth()->user()->id === 1 || auth()->user()->id === 2),

                SelectFilter::make('kelas_id')
                    ->label('Kelas')
                    ->multiple()
                    ->options(Kelas::all()->pluck('kelas', 'id'))
                    ->visible(auth()->user()->id === 1 || auth()->user()->id === 2),

                SelectFilter::make('mapel_id')
                    ->label('Mapel')
                    ->multiple()
                    ->options(Mapel::all()->pluck('mapel', 'id'))
                    ->visible(auth()->user()->id === 1 || auth()->user()->id === 2),

                SelectFilter::make('pengajar_id')
                    ->label('Pengajar')
                    ->multiple()
                    ->options(Pengajar::all()->pluck('nama', 'id'))
                    ->visible(auth()->user()->id === 1 || auth()->user()->id === 2),

                Filter::make('is_nilai_selesai')
                    ->label('Nilai Selesai')
                    ->query(fn (Builder $query): Builder => $query->where('is_nilai_selesai', 1))
                    ->visible(auth()->user()->id === 1 || auth()->user()->id === 2),

                Filter::make('Nilai Belum Selesai')
                    ->label('Nilai Belum Selesai')
                    ->query(fn (Builder $query): Builder => $query->where('is_nilai_selesai', 0))
                    ->visible(auth()->user()->id === 1 || auth()->user()->id === 2),

            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->actions([])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                // Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->emptyStateHeading('Tidak ada data')
            ->emptyStateIcon('heroicon-o-book-open');
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
            'index' => Pages\ListMenuMudirs::route('/'),
            'create' => Pages\CreateMenuMudir::route('/create'),
            'view' => Pages\ViewMenuMudir::route('/{record}'),
            'edit' => Pages\EditMenuMudir::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {

        return parent::getEloquentQuery()->whereIn('qism_id', Auth::user()->mudirqism)->where('is_nilai', 1);
    }
}
