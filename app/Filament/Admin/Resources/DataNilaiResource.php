<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DataNilaiResource\Pages;
use App\Filament\Admin\Resources\DataNilaiResource\RelationManagers;
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

class DataNilaiResource extends Resource
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

    protected static ?string $modelLabel = 'Data Nilai';

    protected static ?string $navigationLabel = 'Data Nilai';

    protected static ?string $pluralModelLabel = 'Data Nilai';

    protected static ?string $model = Nilai::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Soal Nilai')
                    ->schema([
                        Checkbox::make('is_soal')
                            ->label('Soal'),

                        Checkbox::make('is_nilai')
                            ->label('Nilai'),

                        Checkbox::make('is_nilai_selesai')
                            ->label('Status Input Nilai'),
                    ])
                    ->compact()
                    ->columns(1),

                Section::make('Data Soal Nilai')
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
                            ->native(false)
                            ->createOptionForm([

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

                                Select::make('kelas_id')
                                    ->label('Kelas')
                                    ->options(function (Get $get) {
                                        return (QismDetailHasKelas::where('qism_detail_id', $get('qism_detail_id'))->pluck('kelas', 'kelas_id'));
                                    })
                                    ->native(false),

                                TextInput::make('kategori')
                                    ->label('Kategori'),
                            ])->columns(4),

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
                        TextInput::make('file_nilai')
                            ->maxLength(255),
                        TextInput::make('keterangan_nilai')
                            ->label('Keterangan Nilai'),

                    ])
                    ->compact()
                    ->columns(4)


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->defaultPaginationPageOption('all')
            // ->striped()
            ->columns([

                CheckboxColumn::make('is_soal')
                    ->label('Soal')
                    ->alignCenter(),

                CheckboxColumn::make('is_nilai')
                    ->label('Nilai')
                    ->alignCenter(),

                    TextInputColumn::make('soal_siap_print')
                    ->label('Link Soal'),

                    TextInputColumn::make('file_nilai')
                    ->label('Link Nilai'),

                CheckboxColumn::make('is_nilai_selesai')
                    ->label('Status N')
                    ->alignCenter(),


                SelectColumn::make('jenis_soal_id')
                    ->label('Jenis Soal')
                    ->options(JenisSoal::all()->pluck('jenis_soal', 'id'))
                    ->sortable()
                    ->extraAttributes([
                        'style' => 'min-width:230px'
                    ]),

                TextInputColumn::make('kode_soal')
                    ->label('Kode Soal'),

                TextInputColumn::make('keterangan_nilai')
                    ->label('Keterangan Nilai'),

                SelectColumn::make('qism_detail_id')
                    ->label('Qism')
                    ->options(QismDetail::all()->pluck('abbr_qism_detail', 'id'))
                    ->sortable()
                    ->extraAttributes([
                        'style' => 'min-width:40px'
                    ]),

                SelectColumn::make('kelas_id')
                    ->label('Kelas')
                    ->options(Kelas::all()->pluck('kelas', 'id'))
                    ->sortable()
                    ->extraAttributes([
                        'style' => 'min-width:40px'
                    ]),

                TextInputColumn::make('kelas_internal')
                    ->label('Kelas Internal')
                    ->alignCenter()
                    ->searchable(isIndividual: true)
                    ->extraAttributes([
                        'style' => 'max-width:70px'
                    ])
                    ->sortable(),

                SelectColumn::make('kategori_soal_id')
                    ->label('Kategori Soal')
                    ->options(KategoriSoal::all()->pluck('kategori', 'id'))
                    ->sortable()
                    ->extraAttributes([
                        'style' => 'min-width:100px'
                    ]),

                SelectColumn::make('mapel_id')
                    ->label('Mapel')
                    ->options(Mapel::all()->pluck('mapel', 'id'))
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->extraAttributes([
                        'style' => 'min-width:100px'
                    ]),

                // TextColumn::make('pengajar.nama')
                //     ->label('Nama Pengajar')
                //     ->sortable(),

                SelectColumn::make('pengajar_id')
                    ->label('Nama Pengajar')
                    ->options(Pengajar::all()->pluck('nama', 'id'))
                    ->sortable()
                    ->searchable(isIndividual: true)
                    ->hidden(!auth()->user()->id === 1 || !auth()->user()->id === 2)
                    ->placeholder('Pilih Pengajar')
                    ->extraAttributes([
                        'style' => 'min-width:230px'
                    ]),

            ])
            ->groups([
                Group::make('jenisSoal.jenis_soal')
                    ->titlePrefixedWithLabel(false),
                Group::make('qismDetail.id')
                    ->titlePrefixedWithLabel(false)
            ])
            ->defaultSort('kode_soal')
            ->filters([

                SelectFilter::make('jenis_soal_id')
                    ->label('Jenis Soal')
                    ->multiple()
                    ->options([
                        '1' => 'Hifdz',
                        '2' => 'Lainnya',
                        '3' => 'Rapor TA',
                        '4' => 'Tulis/Lisan',
                    ]),

                Filter::make('is_soal')
                    ->label('Hanya Soal')
                    ->query(fn (Builder $query): Builder => $query->where('is_soal', 1)),

                Filter::make('is_nilai')
                    ->label('Hanya Nilai')
                    ->query(fn (Builder $query): Builder => $query->where('is_nilai', 1)),

                Filter::make('is_nilai_selesai')
                    ->label('Nilai Selesai')
                    ->query(fn (Builder $query): Builder => $query->where('is_nilai_selesai', 1)),

                Filter::make('Nilai Belum Selesai')
                    ->label('Nilai Belum Selesai')
                    ->query(fn (Builder $query): Builder => $query->where('is_nilai_selesai', 0)),


                SelectFilter::make('qism_detail_id')
                    ->label('Qism')
                    ->multiple()
                    ->options([
                        '1' => 'TAPa',
                        '2' => 'TAPi',
                        '3' => 'PTPa',
                        '4' => 'PTPi',
                        '5' => 'TQPa',
                        '6' => 'TQPi',
                        '7' => 'IDD',
                        '8' => 'MTW',
                        '9' => 'TN',
                    ]),

                SelectFilter::make('kelas_id')
                    ->label('Kelas')
                    ->multiple()
                    ->options([
                        '1' => 'Kelas 1',
                        '2' => 'Kelas 2',
                        '3' => 'Kelas 3',
                        '4' => 'Kelas 4',
                        '5' => 'Kelas 5',
                        '6' => 'Kelas 6',
                        '7' => 'Kelas A',
                        '8' => 'Kelas B',
                        '9' => 'Kelas MTW',
                    ]),

            ], layout: FiltersLayout::AboveContent)
            ->actions([
                ReplicateAction::make(),

                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([

                Tables\Actions\BulkAction::make('soal')
                    ->label(__('Soal'))
                    ->color('success')
                    // ->requiresConfirmation()
                    // ->modalIcon('heroicon-o-check-circle')
                    // ->modalIconColor('success')
                    // ->modalHeading('Tandai Data sebagai Soal?')
                    // ->modalDescription('Setelah klik tombol "Simpan", maka status akan berubah')
                    // ->modalSubmitActionLabel('Simpan')
                    ->action(fn (Collection $records, array $data) => $records->each(
                        function ($record) {

                            $data['is_soal'] = 1;
                            $record->update($data);

                            return $record;

                            Notification::make()
                                ->success()
                                ->title('Data telah diubah')
                                ->persistent()
                                ->color('Success')
                                ->send();
                        }
                    ))
                    ->deselectRecordsAfterCompletion(),

                Tables\Actions\BulkAction::make('resetsoal')
                    ->label(__('Reset Soal'))
                    ->color('gray')
                    // ->requiresConfirmation()
                    // ->modalIcon('heroicon-o-arrow-path')
                    // ->modalIconColor('gray')
                    // ->modalHeading(new HtmlString('Reset tanda Soal?'))
                    // ->modalDescription('Setelah klik tombol "Simpan", maka status akan berubah')
                    // ->modalSubmitActionLabel('Simpan')
                    ->action(fn (Collection $records, array $data) => $records->each(
                        function ($record) {

                            $data['is_soal'] = 0;
                            $record->update($data);

                            return $record;

                            Notification::make()
                                ->success()
                                ->title('Status Ananda telah diupdate')
                                ->persistent()
                                ->color('Success')
                                ->send();
                        }
                    ))->deselectRecordsAfterCompletion(),

                Tables\Actions\BulkAction::make('nilai')
                    ->label(__('Nilai'))
                    ->color('success')
                    // ->requiresConfirmation()
                    // ->modalIcon('heroicon-o-check-circle')
                    // ->modalIconColor('success')
                    // ->modalHeading('Tandai Data sebagai Nilai?')
                    // ->modalDescription('Setelah klik tombol "Simpan", maka status akan berubah')
                    // ->modalSubmitActionLabel('Simpan')
                    ->action(fn (Collection $records, array $data) => $records->each(
                        function ($record) {

                            $data['is_nilai'] = 1;
                            $record->update($data);

                            return $record;

                            Notification::make()
                                ->success()
                                ->title('Data telah diubah')
                                ->persistent()
                                ->color('Success')
                                ->send();
                        }
                    ))
                    ->deselectRecordsAfterCompletion(),

                Tables\Actions\BulkAction::make('resetnilai')
                    ->label(__('Reset Nilai'))
                    ->color('gray')
                    // ->requiresConfirmation()
                    // ->modalIcon('heroicon-o-arrow-path')
                    // ->modalIconColor('gray')
                    // ->modalHeading(new HtmlString('Reset tanda Nilai?'))
                    // ->modalDescription('Setelah klik tombol "Simpan", maka status akan berubah')
                    // ->modalSubmitActionLabel('Simpan')
                    ->action(fn (Collection $records, array $data) => $records->each(
                        function ($record) {

                            $data['is_nilai'] = 0;
                            $record->update($data);

                            return $record;

                            Notification::make()
                                ->success()
                                ->title('Status Ananda telah diupdate')
                                ->persistent()
                                ->color('Success')
                                ->send();
                        }
                    ))->deselectRecordsAfterCompletion(),

                Tables\Actions\BulkAction::make('soalnilai')
                    ->label(__('Soal & Nilai'))
                    ->color('success')
                    // ->requiresConfirmation()
                    // ->modalIcon('heroicon-o-check-circle')
                    // ->modalIconColor('success')
                    // ->modalHeading('Tandai Data sebagai Soal?')
                    // ->modalDescription('Setelah klik tombol "Simpan", maka status akan berubah')
                    // ->modalSubmitActionLabel('Simpan')
                    ->action(fn (Collection $records, array $data) => $records->each(
                        function ($record) {

                            $data['is_soal'] = 1;
                            $data['is_nilai'] = 1;
                            $record->update($data);

                            return $record;

                            Notification::make()
                                ->success()
                                ->title('Data telah diubah')
                                ->persistent()
                                ->color('Success')
                                ->send();
                        }
                    ))
                    ->deselectRecordsAfterCompletion(),

                Tables\Actions\BulkAction::make('resetsoalnilai')
                    ->label(__('Reset Soal & Nilai'))
                    ->color('gray')
                    // ->requiresConfirmation()
                    // ->modalIcon('heroicon-o-arrow-path')
                    // ->modalIconColor('gray')
                    // ->modalHeading(new HtmlString('Reset tanda Soal?'))
                    // ->modalDescription('Setelah klik tombol "Simpan", maka status akan berubah')
                    // ->modalSubmitActionLabel('Simpan')
                    ->action(fn (Collection $records, array $data) => $records->each(
                        function ($record) {

                            $data['is_soal'] = 0;
                            $data['is_nilai'] = 0;
                            $record->update($data);

                            return $record;

                            Notification::make()
                                ->success()
                                ->title('Status Ananda telah diupdate')
                                ->persistent()
                                ->color('Success')
                                ->send();
                        }
                    ))->deselectRecordsAfterCompletion(),

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
            'index' => Pages\ListDataNilais::route('/'),
            'create' => Pages\CreateDataNilai::route('/create'),
            'view' => Pages\ViewDataNilai::route('/{record}'),
            'edit' => Pages\EditDataNilai::route('/{record}/edit'),
        ];
    }
}
