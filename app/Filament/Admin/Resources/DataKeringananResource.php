<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DataKeringananResource\Pages;
use App\Filament\Admin\Resources\DataKeringananResource\RelationManagers;
use App\Models\JenisSoal;
use App\Models\KategoriSoal;
use App\Models\Kelas;
use App\Models\KelasSantri;
use App\Models\Mahad;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\Pengajar;
use App\Models\Qism;
use App\Models\QismDetail;
use App\Models\QismDetailHasKelas;
use App\Models\Santri;
use App\Models\Semester;
use App\Models\StaffAdmin;
use App\Models\TahunAjaran;
use Filament\Actions\Action;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Filament\Tables\Actions\Action as RowAction;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\HtmlString;
use Filament\Tables\Filters\Filter;

class DataKeringananResource extends Resource
{
    public static function canCreate(): bool
    {
        return auth()->user()->id == 1;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->id == 1;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->id == 1;
        // return false;
    }


    protected static ?string $navigationGroup = 'PSB';

    protected static ?int $navigationSort = 01010;

    protected static ?string $modelLabel = 'Data Keringanan';

    protected static ?string $navigationLabel = 'Data Keringanan';

    protected static ?string $pluralModelLabel = 'Data Keringanan';

    protected static ?string $model = Santri::class;

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
            ->defaultPaginationPageOption('20')
            ->columns([
                TextColumn::make('walisantri.nama_kpl_kel_santri')
                    ->label('Walisantri')
                    ->sortable(),

                TextColumn::make('walisantri.hp_komunikasi')
                    ->label('HP')
                    ->sortable(),

                TextColumn::make('nama_lengkap')
                    ->label('Santri')
                    ->sortable(),

                TextColumn::make('jenispendaftar')
                    ->label('Jenis')
                    ->sortable(),

                TextColumn::make('file_kk')
                    ->label('Kartu Keluarga')
                    ->description(fn (): string => 'Kartu Keluarga', position: 'above')
                    // ->color('white')
                    ->formatStateUsing(fn (string $state): string => __("Lihat"))
                    // ->limit(1)
                    ->icon('heroicon-s-eye')
                    ->iconColor('success')
                    // ->circular()
                    ->alignCenter()
                    ->placeholder(new HtmlString('Kartu Keluarga<br>Belum Upload'))
                    ->url(function (Model $record) {
                        if ($record->file_kk !== null) {

                            return ("https://psb.tsn.ponpes.id/storage/" . $record->file_kk);
                        }
                    })
                    ->badge()
                    ->color('gray')
                    ->openUrlInNewTab(),

                TextColumn::make('file_skt')
                    ->label('Surat Keterangan Taklim')
                    ->description(fn (): string => 'Surat Keterangan Taklim', position: 'above')
                    // ->color('white')
                    ->formatStateUsing(fn (string $state): string => __("Lihat"))
                    // ->limit(1)
                    ->icon('heroicon-s-eye')
                    ->iconColor('success')
                    // ->circular()
                    ->alignCenter()
                    ->placeholder(new HtmlString('Surat Keterangan Taklim<br>Belum Upload'))
                    ->url(function (Model $record) {
                        if ($record->file_skt !== null) {

                            return ("https://psb.tsn.ponpes.id/storage/" . $record->file_skt);
                        }
                    })
                    ->badge()
                    ->color('gray')
                    ->openUrlInNewTab(),

                TextColumn::make('file_spkm')
                    ->label('Surat Pernyataan Kesanggupan')
                    ->description(fn (): string => 'Surat Pernyataan Kesanggupan', position: 'above')
                    // ->color('white')
                    ->formatStateUsing(fn (string $state): string => __("Lihat"))
                    // ->limit(1)
                    ->icon('heroicon-s-eye')
                    ->iconColor('success')
                    // ->circular()
                    ->alignCenter()
                    ->placeholder(new HtmlString('Surat Pernyataan Kesanggupan<br>Belum Upload'))
                    ->url(function (Model $record) {
                        if ($record->file_spkm !== null) {

                            return ("https://psb.tsn.ponpes.id/storage/" . $record->file_spkm);
                        }
                    })
                    ->badge()
                    ->color('gray')
                    ->openUrlInNewTab(),

                TextColumn::make('file_pka')
                    ->label('Surat Permohonan Keringanan Administrasi')
                    ->description(fn (): string => 'Surat Permohonan Keringanan Administrasi', position: 'above')
                    // ->color('white')
                    ->formatStateUsing(fn (string $state): string => __("Lihat"))
                    // ->limit(1)
                    ->icon('heroicon-s-eye')
                    ->iconColor('success')
                    // ->circular()
                    ->alignCenter()
                    ->placeholder(new HtmlString('Surat Permohonan Keringanan Administrasi<br>Belum Upload'))
                    ->url(function (Model $record) {
                        if ($record->file_pka !== null) {

                            return ("https://psb.tsn.ponpes.id/storage/" . $record->file_pka);
                        }
                    })
                    ->badge()
                    ->color('gray')
                    ->openUrlInNewTab(),

                TextColumn::make('file_ktmu')
                    ->label('Surat Keterangan Tidak Mampu (U)')
                    ->description(fn (): string => 'Surat Keterangan Tidak Mampu (U)', position: 'above')
                    // ->color('white')
                    ->formatStateUsing(fn (string $state): string => __("Lihat"))
                    // ->limit(1)
                    ->icon('heroicon-s-eye')
                    ->iconColor('success')
                    // ->circular()
                    ->alignCenter()
                    ->placeholder(new HtmlString('Surat Keterangan Tidak Mampu (U)<br>Belum Upload'))
                    ->url(function (Model $record) {
                        if ($record->file_ktmu !== null) {

                            return ("https://psb.tsn.ponpes.id/storage/" . $record->file_ktmu);
                        }
                    })
                    ->badge()
                    ->color('gray')
                    ->openUrlInNewTab(),

                TextColumn::make('file_ktmp')
                    ->label('Surat Keterangan Tidak Mampu (P)')
                    ->description(fn (): string => 'Surat Keterangan Tidak Mampu (P)', position: 'above')
                    // ->color('white')
                    ->formatStateUsing(fn (string $state): string => __("Lihat"))
                    // ->limit(1)
                    ->icon('heroicon-s-eye')
                    ->iconColor('success')
                    // ->circular()
                    ->alignCenter()
                    ->placeholder(new HtmlString('Surat Keterangan Tidak Mampu (P)<br>Belum Upload'))
                    ->url(function (Model $record) {
                        if ($record->file_ktmp !== null) {

                            return ("https://psb.tsn.ponpes.id/storage/" . $record->file_ktmp);
                        }
                    })
                    ->badge()
                    ->color('gray')
                    ->openUrlInNewTab(),

                TextColumn::make('pendaftar.ps_kadm_status')
                    ->label('Status')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),


            ])
            ->defaultSort('walisantri.nama_kpl_kel_santri')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListDataKeringanans::route('/'),
            'create' => Pages\CreateDataKeringanan::route('/create'),
            'view' => Pages\ViewDataKeringanan::route('/{record}'),
            'edit' => Pages\EditDataKeringanan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {

        return parent::getEloquentQuery()->whereHas('pendaftar', function ($query) {
            $query->where('ps_kadm_status', 'Santri/Santriwati tidak mampu');
        })->where('jenispendaftar', '!=', null)->where(function ($query) {
            $query->where('status_tahap', 'Diterima')
                ->orWhere('status_tahap', 'Lolos');
        });
    }
}
