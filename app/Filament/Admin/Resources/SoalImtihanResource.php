<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SoalImtihanResource\Pages;
use App\Filament\Admin\Resources\SoalImtihanResource\RelationManagers;
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
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Filament\Tables\Actions\Action as RowAction;
use Illuminate\Support\HtmlString;

class SoalImtihanResource extends Resource
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

    protected static ?string $navigationGroup = 'Imtihan';

    protected static ?int $navigationSort = 03010;

    protected static ?string $modelLabel = 'Soal Imtihan';

    protected static ?string $navigationLabel = 'Soal Imtihan';

    protected static ?string $pluralModelLabel = 'Soal Imtihan';

    protected static ?string $model = Nilai::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('jenis_soal_id')
                    ->label('Jenis Soal')
                    ->options(JenisSoal::all()->pluck('jenis_soal', 'id'))
                    ->native(false),

                Select::make('mahad_id')
                    ->label('Mahad')
                    ->options(Mahad::all()->pluck('mahad', 'id'))
                    ->native(false)
                    ->default('1'),

                Select::make('qism_id')
                    ->label('Qism')
                    ->options(Qism::all()->pluck('qism', 'id'))
                    ->native(false),

                Select::make('qism_detail_id')
                    ->label('Qism Detail')
                    ->options(fn (Get $get): Collection => QismDetail::query()
                        ->where('qism_id', $get('qism_id'))
                        ->pluck('abbr_qism_detail', 'id'))
                    ->native(false),

                Select::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->options(TahunAjaran::all()->pluck('ta', 'id'))
                    ->native(false)
                    ->default('5'),

                Select::make('semester_id')
                    ->label('Semester')
                    ->options(Semester::all()->pluck('semester', 'id'))
                    ->native(false)
                    ->default('2'),

                Select::make('kelas_id')
                    ->label('Kelas')
                    ->options(function (Get $get) {
                        return (QismDetailHasKelas::where('qism_detail_id', $get('qism_detail_id'))->pluck('kelas', 'kelas_id'));
                    })
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {



                        $getqismdetail = $get('qism_detail_id');

                        $gettahunajaran = $get('tahun_ajaran_id');

                        $getsemester = $get('semester_id');

                        $getkelas = $get('kelas_id');




                        $santri = KelasSantri::where('qism_detail_id', $getqismdetail)
                            ->where('tahun_ajaran_id', $gettahunajaran)
                            ->where('semester_id', $getsemester)
                            ->where('kelas_id', $getkelas)
                            ->count();

                        // $jumlahsantri = ;

                        // dd($santri);
                        $set('jumlah_print', $santri);
                    }),

                TextInput::make('kelas_internal')
                    ->label('Kelas Internal'),

                Select::make('mapel_id')
                    ->relationship(name: 'mapel', titleAttribute: 'mapel')
                    ->label('Mapel')
                    ->options(Mapel::all()->pluck('mapel', 'id'))
                    ->native(false)
                    ->searchable()
                    ->createOptionForm([
                        TextInput::make('mapel')
                            ->required(),
                    ])
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {

                        $getqismdetail = QismDetail::where('id', $get('qism_detail_id'))->first();
                        $qismdetail = $getqismdetail->abbr_qism_detail;

                        $gettahunajaran = TahunAjaran::where('id', $get('tahun_ajaran_id'))->first();
                        $tahunajaran = $gettahunajaran->abbr_ta;

                        $getkelas = Kelas::where('id', $get('kelas_id'))->first();
                        $kelas = $getkelas->abbr_kelas;

                        $getkelasinternal = $get('kelas_internal');

                        // dd($getkelasinternal);

                        $getsemester = Semester::where('id', $get('semester_id'))->first();
                        $semester = $getsemester->abbr_semester;

                        $getmapel = Mapel::where('id', $get('mapel_id'))->first();
                        $mapel = $getmapel->mapel;

                        if ($getkelasinternal === null) {
                            $kodesoal = $qismdetail . "-" . $tahunajaran . "-" . $semester . "-" . $kelas . "-" . $mapel;

                            $set('kode_soal', $kodesoal);
                        } elseif ($getkelasinternal !== null) {
                            $kodesoal = $qismdetail . "-" . $tahunajaran . "-" . $semester . "-" . $getkelasinternal . "-" . $mapel;

                            $set('kode_soal', $kodesoal);
                        }
                    }),

                Select::make('kategori_soal_id')
                    ->label('Kategori')
                    ->options(function (Get $get) {
                        return (KategoriSoal::where('qism_id', $get('qism_id'))
                            ->where('qism_detail_id', $get('qism_detail_id'))
                            ->pluck('kategori', 'id'));
                    })
                    ->native(false),

                Select::make('pengajar_id')
                    ->label('Pengajar')
                    ->options(Pengajar::all()->pluck('nama', 'id'))
                    ->native(false),

                Select::make('staff_admin_id')
                    ->label('PIC')
                    ->options(StaffAdmin::all()->pluck('nama_staff', 'id'))
                    ->native(false),

                TextInput::make('kode_soal')
                    ->maxLength(255),
                TextInput::make('soal_dari_ustadz')
                    ->maxLength(255),
                TextInput::make('soal_siap_print')
                    ->maxLength(255),
                TextInput::make('jumlah_print')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->paginated(false)
            ->striped()
            ->columns([

                TextColumn::make('kode_soal')
                    ->label('Kode Soal'),

                TextColumn::make('qismDetail.abbr_qism_detail')
                    ->label('Qism')
                    ->hidden()
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('kelas.abbr_kelas')
                    ->label('Kelas')
                    ->alignCenter()
                    ->sortable(),

                TextInputColumn::make('kelas_internal')
                    ->label('Kelas Internal')
                    ->alignCenter()
                    ->extraAttributes([
                        'style' => 'max-width:70px'
                    ])
                    ->sortable(),

                TextColumn::make('mapel.mapel')
                    ->label('Mapel')
                    ->sortable(),

                // TextColumn::make('pengajar.nama')
                //     ->label('Nama Pengajar')
                //     ->sortable(),

                SelectColumn::make('pengajar_id')
                    ->label('Nama Pengajar')
                    ->options(Pengajar::all()->pluck('nama', 'id'))
                    ->sortable()
                    ->searchable()
                    ->hidden(!auth()->user()->id === 1 || !auth()->user()->id === 2)
                    ->placeholder('Pilih Pengajar')
                    ->extraAttributes([
                        'style' => 'min-width:230px'
                    ]),

                SelectColumn::make('staff_admin_id')
                    ->label('PIC')
                    ->options(StaffAdmin::all()->pluck('nama_staff', 'id'))
                    ->sortable()
                    ->placeholder('Pilih PIC')
                    ->extraAttributes([
                        'style' => 'min-width:230px'
                    ]),

                TextColumn::make('soal_dari_ustadz')
                    ->label('Draft Soal')
                    ->formatStateUsing(fn (string $state): string => __("Lihat"))
                    // ->limit(1)
                    ->icon('heroicon-s-eye')
                    ->iconColor('success')
                    ->alignCenter()
                    ->url(function (Model $record) {
                        return ($record->soal_dari_ustadz);
                    })
                    ->badge()
                    ->color('gray')
                    ->openUrlInNewTab(),

                CheckboxColumn::make('status_soal')
                    ->label('Status Pembuatan Soal')
                    ->alignCenter(),

                TextColumn::make('soal_siap_print')
                    ->label('Soal')
                    ->formatStateUsing(fn (string $state): string => __("Lihat"))
                    // ->limit(1)
                    ->icon('heroicon-s-eye')
                    ->iconColor('success')
                    ->alignCenter()
                    ->url(function (Model $record) {
                        return ($record->soal_siap_print);
                    })
                    ->badge()
                    ->color('gray')
                    ->openUrlInNewTab(),

                TextInputColumn::make('jumlah_print')
                    ->label('Jumlah Santri')
                    ->alignCenter()
                    ->extraAttributes([
                        'style' => 'max-width:70px'
                    ]),

                TextColumn::make('total_print')
                    ->label('Jumlah Print')
                    ->getStateUsing(function (Model $record): float {
                        if ($record->jumlah_print === null) {
                            return 0;
                        } elseif ($record->jumlah_print !== null) {
                            return $record->jumlah_print + 1;
                        }
                    }),

                CheckboxColumn::make('status_print')
                    ->label('Status Print')
                    ->alignCenter(),
            ])
            ->groups([
                Group::make('qismDetail.abbr_qism_detail')
                    ->titlePrefixedWithLabel(false)
            ])

            ->defaultGroup('qismDetail.abbr_qism_detail')
            ->defaultSort('kode_soal')
            ->filters([
                //
            ])
            ->actions([
                RowAction::make('reset_jumlah_print')
                    ->label(__('Reset Jumlah Print'))
                    ->button()
                    ->outlined()
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-exclamation-triangle')
                    ->modalIconColor('danger')
                    ->modalHeading(new HtmlString('Reset Jumlah Print?'))
                    ->modalDescription('Setelah klik tombol "Simpan", maka jumlah print akan ter-reset sesuai jumlah santri per kelas')
                    ->modalSubmitActionLabel('Simpan')
                    ->action(function (Model $record) {
                        $santri = KelasSantri::where('qism_detail_id', $record->qism_detail_id)
                            ->where('tahun_ajaran_id', $record->tahun_ajaran_id)
                            ->where('semester_id', $record->semester_id)
                            ->where('kelas_id', $record->kelas_id)
                            ->count();

                        $data['jumlah_print'] = $santri;
                        $record->update($data);

                        return $record;

                        // dd($santri);
                    }),

                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([

                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSoalImtihans::route('/'),
            'create' => Pages\CreateSoalImtihan::route('/create'),
            'view' => Pages\ViewSoalImtihan::route('/{record}'),
            'edit' => Pages\EditSoalImtihan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {

        // return parent::getEloquentQuery()->where('qism_id', Auth::user()->mudirqism)->orWhere('');

        return parent::getEloquentQuery()->where('is_soal', 1);
    }
}
