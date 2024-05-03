<?php

namespace App\Filament\Walisantri\Resources\DataSantriResource\Widgets;

use App\Models\KelasSantri;
use App\Models\Santri as ModelsSantri;
use App\Models\TahunBerjalan;
use App\Models\Walisantri;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use App\Models\Kesantrian\DataSantri;
use App\Models\QismDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use App\Models\Kelas;
use App\Models\KeteranganStatusSantri;
use App\Models\StatusSantri;
use App\Models\User;
use Filament\Forms\Components\Select;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Kodepos;
use App\Models\NismPerTahun;
use App\Models\Pendaftar;
use App\Models\Provinsi;
use App\Models\Qism;
use App\Models\QismDetailHasKelas;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Closure;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Illuminate\Support\Str;
use stdClass;
use Filament\Tables\Grouping\Group as GroupingGroup;

class Santri extends BaseWidget
{

    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {

        $tahunberjalanaktif = TahunBerjalan::where('is_active', 1)->first();
        $ts = TahunBerjalan::where('tb', $tahunberjalanaktif->ts)->first();

        return $table
            ->heading('Daftar Santri')
            ->description('Silakan melengkapi data Walisantri dan Santri untuk keperluan kelengkapan data EMIS KEMENAG.  Jazaakumullahu khoiron.')
            ->paginated(false)
            ->query(

                KelasSantri::where('tahun_berjalan_id', $tahunberjalanaktif->id)->where('kartu_keluarga', Auth::user()->username)->whereHas('statussantri', function ($query) {
                    $query->where('status', 'aktif');
                })
            )
            ->columns([
                Stack::make([
                    TextColumn::make('santri.nama_lengkap')
                        ->label('Nama')
                        ->size(TextColumn\TextColumnSize::Large)
                        ->weight(FontWeight::Bold),

                    TextColumn::make('qism_detail.qism_detail')
                        ->label('Qism')
                        ->weight(FontWeight::Bold),

                    TextColumn::make('kelas.kelas')
                        ->label('Kelas')
                        ->weight(FontWeight::Bold),

                    TextColumn::make('tanggalupdate')
                        ->weight(FontWeight::Bold)
                        ->default(new HtmlString('</br>')),

                    TextColumn::make('walisantri.ws_emis4')
                        ->label('Status Data Walisantri')
                        ->default('Belum Lengkap')
                        ->size(TextColumn\TextColumnSize::Large)
                        ->weight(FontWeight::Bold)
                        ->description(fn ($record): string => "Status Data Walisantri:", position: 'above')
                        ->formatStateUsing(function (Model $record) {
                            $wsemis4 = Walisantri::where('id', $record->walisantri_id)->first();
                            // dd($pendaftar->ps_kadm_status);
                            if ($wsemis4->ws_emis4 === null) {
                                return ('Belum lengkap');
                            } elseif ($wsemis4->ws_emis4 !== null) {
                                return ('Lengkap');
                            }
                        })
                        ->badge()
                        ->color(function (Model $record) {
                            $wsemis4 = Walisantri::where('id', $record->walisantri_id)->first();
                            // dd($pendaftar->ps_kadm_status);
                            if ($wsemis4->ws_emis4 === null) {
                                return ('danger');
                            } elseif ($wsemis4->ws_emis4 !== null) {
                                return ('success');
                            }
                        }),

                    TextColumn::make('santri.s_emis4')
                        ->label('Status Data Santri')
                        ->default('Belum Lengkap')
                        ->size(TextColumn\TextColumnSize::Large)
                        ->weight(FontWeight::Bold)
                        ->description(fn ($record): string => "Status Data Santri:", position: 'above')
                        ->formatStateUsing(function (Model $record) {
                            $semis4 = ModelsSantri::where('id', $record->santri_id)->first();
                            // dd($pendaftar->ps_kadm_status);
                            if ($semis4->s_emis4 === null) {
                                return ('Belum lengkap');
                            } elseif ($semis4->s_emis4 !== null) {
                                return ('Lengkap');
                            }
                        })
                        ->badge()
                        ->color(function (Model $record) {
                            $semis4 = ModelsSantri::where('id', $record->santri_id)->first();
                            // dd($pendaftar->ps_kadm_status);
                            if ($semis4->s_emis4 === null) {
                                return ('danger');
                            } elseif ($semis4->s_emis4 !== null) {
                                return ('success');
                            }
                        }),

                    TextColumn::make('a')
                        ->default(new HtmlString('</br>Silakan melengkapi data Walisantri dan Santri dengan klik tombol di bawah ini')),

                ])
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 2,
            ])
            ->actions([

                Tables\Actions\EditAction::make()
                    ->label('Edit Data')
                    ->modalCloseButton(false)
                    ->modalHeading(' ')
                    // ->modalDescription(new HtmlString('<div class="">
                    //                                         <p>Butuh bantuan?</p>
                    //                                         <p>Silakan mengubungi admin di bawah ini:</p>

                    //                                         <table class="table w-fit">
                    //                     <!-- head -->
                    //                     <thead>
                    //                         <tr class="border-tsn-header">
                    //                             <th class="text-tsn-header text-xs" colspan="2"></th>
                    //                         </tr>
                    //                     </thead>
                    //                     <tbody>
                    //                         <!-- row 1 -->
                    //                         <tr>
                    //                             <th><a href="https://wa.me/6282210862400"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    //                             <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
                    //                             </svg>
                    //                             </a></th>
                    //                             <td class="text-xs"><a href="https://wa.me/6282210862400">WA Admin Putra (Abu Hammaam)</a></td>
                    //                         </tr>
                    //                         <tr>
                    //                             <th><a href="https://wa.me/6285236459012"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    //                             <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
                    //                             </svg>
                    //                             </a></th>
                    //                             <td class="text-xs"><a href="https://wa.me/6285236459012">WA Admin Putra (Abu Fathimah Hendi)</a></td>
                    //                         </tr>
                    //                         <tr>
                    //                             <th><a href="https://wa.me/6281333838691"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    //                             <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
                    //                             </svg>
                    //                             </a></th>
                    //                             <td class="text-xs"><a href="https://wa.me/6281333838691">WA Admin Putra (Akh Irfan)</a></td>
                    //                         </tr>
                    //                         <tr>
                    //                             <th><a href="https://wa.me/628175765767"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    //                             <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
                    //                             </svg>
                    //                             </a></th>
                    //                             <td class="text-xs"><a href="https://wa.me/628175765767">WA Admin Putri</a></td>
                    //                         </tr>


                    //                     </tbody>
                    //                     </table>

                    //                                     </div>'))
                    ->modalWidth('full')
                    // ->stickyModalHeader()
                    ->button()
                    ->closeModalByClickingAway(false)
                    ->modalSubmitActionLabel('Simpan')
                    ->modalCancelAction(fn (StaticAction $action) => $action->label('Batal'))
                    ->form([

                        Section::make()
                            ->schema([

                                Placeholder::make('')
                                    ->content(function (Model $record) {
                                        $santri = ModelsSantri::where('id', $record->santri_id)->first();
                                        return (new HtmlString('<div><p class="text-3xl"><strong>' . $santri->nama_lengkap . '</strong></p></div>'));
                                    }),

                                Placeholder::make('')
                                    ->content(function (Model $record) {
                                        $santri = ModelsSantri::where('id', $record->santri_id)->first();
                                        $tahunberjalanaktif = TahunBerjalan::where('is_active', 1)->first();
                                        $ts = TahunBerjalan::where('tb', $tahunberjalanaktif->ts)->first();

                                        $cekdatats = KelasSantri::where('tahun_berjalan_id', $ts->id)
                                            ->where('santri_id', $record->santri_id)->count();

                                        if ($cekdatats !== 0) {
                                            $tahunberjalanaktif = TahunBerjalan::where('is_active', 1)->first();
                                            $ts = TahunBerjalan::where('tb', $tahunberjalanaktif->ts)->first();

                                            $cekdatats = KelasSantri::where('tahun_berjalan_id', $ts->id)
                                                ->where('santri_id', $record->santri_id)->first();

                                            $abbrqism = Qism::where('id', $cekdatats->qism_id)->first();

                                            $abbrkelas = Kelas::where('id', $cekdatats->kelas_id)->first();


                                            return (new HtmlString('<div class="">
                                            <table class="table w-fit">
                        <!-- head -->
                        <thead>
                            <tr class="border-tsn-header">
                                <th class="text-tsn-header text-xl" colspan="3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- row 1 -->
                            <tr>
                                <th class="text-xl">Qism</th>
                                <td class="text-xl">:</td>
                                <td class="text-xl">' . $abbrqism->qism . '</td>
                            </tr>
                            <tr>
                                <th class="text-xl">Kelas</th>
                                <td class="text-xl">:</td>
                                <td class="text-xl">' . $abbrkelas->kelas . '</td>
                            </tr>
                            <tr>
                                <th class="text-xl">NISM</th>
                                <td class="text-xl">:</td>
                                <td class="text-xl">' . $santri->nism . '</td>
                            </tr>



                        </tbody>
                        </table>

                                        </div>'));
                                        } elseif ($cekdatats === 0) {
                                            $tahunberjalanaktif = TahunBerjalan::where('is_active', 1)->first();
                                            $ts = TahunBerjalan::where('tb', $tahunberjalanaktif->ts)->first();

                                            $cekdatats = KelasSantri::where('tahun_berjalan_id', $tahunberjalanaktif->id)
                                                ->where('santri_id', $record->santri_id)->first();

                                            $abbrqism = Qism::where('id', $cekdatats->qism_id)->first();

                                            $abbrkelas = Kelas::where('id', $cekdatats->kelas_id)->first();




                                            return (new HtmlString('<div class="">
                                            <table class="table w-fit">
                        <!-- head -->
                        <thead>
                            <tr class="border-tsn-header">
                                <th class="text-tsn-header text-xl" colspan="3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- row 1 -->
                            <tr>
                                <th class="text-xl">Qism</th>
                                <td class="text-xl">:</td>
                                <td class="text-xl">' . $abbrqism->qism . '</td>
                            </tr>
                            <tr>
                                <th class="text-xl">Kelas</th>
                                <td class="text-xl">:</td>
                                <td class="text-xl">' . $abbrkelas->kelas . '</td>
                            </tr>
                            <tr>
                                <th class="text-xl">NISM</th>
                                <td class="text-xl">:</td>
                                <td class="text-xl">' . $santri->nism . '</td>
                            </tr>



                        </tbody>
                        </table>

                                        </div>'));
                                        }
                                    }),

                            ]),

                        Tabs::make('Tabs')
                            ->tabs([

                                Tabs\Tab::make('Walisantri')
                                    ->schema([

                                        Group::make()
                                            ->relationship('walisantri')
                                            ->schema([

                                                //AYAH KANDUNG
                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b"><p class="text-lg strong"><strong>AYAH KANDUNG</strong></p></div>')),

                                                TextInput::make('ak_nama_lengkap')
                                                    ->label('Nama Lengkap')
                                                    ->hint('Isi sesuai dengan KK')
                                                    ->hintColor('danger')
                                                    ->required()
                                                    // ->disabled(fn (Get $get) =>
                                                    // $get('ak_nama_lengkap_sama') === 'Ya')
                                                    ->dehydrated(),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>STATUS AYAH KANDUNG</strong></p>
                                                       </div>')),

                                                Select::make('ak_status')
                                                    ->label('Status')
                                                    ->placeholder('Pilih Status')
                                                    ->options([
                                                        'Masih Hidup' => 'Masih Hidup',
                                                        'Sudah Meninggal' => 'Sudah Meninggal',
                                                        'Tidak Diketahui' => 'Tidak Diketahui',
                                                    ])
                                                    ->required()
                                                    ->live()
                                                    ->native(false),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                       </div>')),

                                                TextInput::make('ak_nama_kunyah')
                                                    ->label('Nama Hijroh/Islami')
                                                    ->required()
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ak_status') !== 'Masih Hidup'),

                                                Select::make('ak_kewarganegaraan')
                                                    ->label('Kewarganegaraan')
                                                    ->placeholder('Pilih Kewarganegaraan')
                                                    ->options([
                                                        'WNI' => 'WNI',
                                                        'WNA' => 'WNA',
                                                    ])
                                                    ->required()
                                                    ->live()
                                                    ->native(false)
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ak_status') !== 'Masih Hidup'),

                                                TextInput::make('ak_nik')
                                                    ->label('NIK')
                                                    ->hint('Isi sesuai dengan KK')
                                                    ->hintColor('danger')
                                                    ->length(16)
                                                    ->required()
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ak_kewarganegaraan') !== 'WNI' ||
                                                        $get('ak_status') !== 'Masih Hidup'),

                                                Grid::make(2)
                                                    ->schema([

                                                        TextInput::make('ak_asal_negara')
                                                            ->label('Asal Negara')
                                                            ->required()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ak_kewarganegaraan') !== 'WNA' ||
                                                                $get('ak_status') !== 'Masih Hidup'),

                                                        TextInput::make('ak_kitas')
                                                            ->label('KITAS')
                                                            ->hint('Nomor Izin Tinggal (KITAS)')
                                                            ->hintColor('danger')
                                                            ->required()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ak_kewarganegaraan') !== 'WNA' ||
                                                                $get('ak_status') !== 'Masih Hidup'),
                                                    ]),
                                                Grid::make(2)
                                                    ->schema([

                                                        TextInput::make('ak_tempat_lahir')
                                                            ->label('Tempat Lahir')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->required()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ak_status') !== 'Masih Hidup'),

                                                        DatePicker::make('ak_tanggal_lahir')
                                                            ->label('Tanggal Lahir')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            // ->helperText('Format: bulan/hari/tahun')
                                                            ->required()
                                                            ->format('d/m/Y')
                                                            ->displayFormat('d/m/Y')
                                                            ->closeOnDateSelection()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ak_status') !== 'Masih Hidup'),
                                                    ]),

                                                Grid::make(3)
                                                    ->schema([

                                                        Select::make('ak_pend_terakhir')
                                                            ->label('Pendidikan Terakhir')
                                                            ->placeholder('Pilih Pendidikan Terakhir')
                                                            ->options([
                                                                'SD/Sederajat' => 'SD/Sederajat',
                                                                'SMP/Sederajat' => 'SMP/Sederajat',
                                                                'SMA/Sederajat' => 'SMA/Sederajat',
                                                                'D1' => 'D1',
                                                                'D2' => 'D2',
                                                                'D3' => 'D3',
                                                                'D4/S1' => 'D4/S1',
                                                                'S2' => 'S2',
                                                                'S3' => 'S3',
                                                                'Tidak Bersekolah' => 'Tidak Bersekolah',
                                                            ])
                                                            ->searchable()
                                                            ->required()
                                                            ->native(false)
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ak_status') !== 'Masih Hidup'),

                                                        Select::make('ak_pekerjaan_utama')
                                                            ->label('Pekerjaan Utama')
                                                            ->placeholder('Pilih Pekerjaan Utama')
                                                            ->options([
                                                                'Tidak Bekerja' => 'Tidak Bekerja',
                                                                'Pensiunan' => 'Pensiunan',
                                                                'PNS' => 'PNS',
                                                                'TNI/Polisi' => 'TNI/Polisi',
                                                                'Guru/Dosen' => 'Guru/Dosen',
                                                                'Pegawai Swasta' => 'Pegawai Swasta',
                                                                'Wiraswasta' => 'Wiraswasta',
                                                                'Pengacara/Jaksa/Hakim/Notaris' => 'Pengacara/Jaksa/Hakim/Notaris',
                                                                'Seniman/Pelukis/Artis/Sejenis' => 'Seniman/Pelukis/Artis/Sejenis',
                                                                'Dokter/Bidan/Perawat' => 'Dokter/Bidan/Perawat',
                                                                'Pilot/Pramugara' => 'Pilot/Pramugara',
                                                                'Pedagang' => 'Pedagang',
                                                                'Petani/Peternak' => 'Petani/Peternak',
                                                                'Nelayan' => 'Nelayan',
                                                                'Buruh (Tani/Pabrik/Bangunan)' => 'Buruh (Tani/Pabrik/Bangunan)',
                                                                'Sopir/Masinis/Kondektur' => 'Sopir/Masinis/Kondektur',
                                                                'Politikus' => 'Politikus',
                                                                'Lainnya' => 'Lainnya',
                                                            ])
                                                            ->searchable()
                                                            ->required()
                                                            ->native(false)
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ak_status') !== 'Masih Hidup'),

                                                        Select::make('ak_pghsln_rt')
                                                            ->label('Penghasilan Rata-Rata')
                                                            ->placeholder('Pilih Penghasilan Rata-Rata')
                                                            ->options([
                                                                'Kurang dari 500.000' => 'Kurang dari 500.000',
                                                                '500.000 - 1.000.000' => '500.000 - 1.000.000',
                                                                '1.000.001 - 2.000.000' => '1.000.001 - 2.000.000',
                                                                '2.000.001 - 3.000.000' => '2.000.001 - 3.000.000',
                                                                '3.000.001 - 5.000.000' => '3.000.001 - 5.000.000',
                                                                'Lebih dari 5.000.000' => 'Lebih dari 5.000.000',
                                                                'Tidak ada' => 'Tidak ada',
                                                            ])
                                                            ->searchable()
                                                            ->required()
                                                            ->native(false)
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ak_status') !== 'Masih Hidup'),
                                                    ]),

                                                Grid::make(1)
                                                    ->schema([

                                                        Radio::make('ak_tdk_hp')
                                                            ->label('Memiliki nomor handphone?')
                                                            ->live()
                                                            ->required()
                                                            ->options([
                                                                'Ya' => 'Ya',
                                                                'Tidak' => 'Tidak',
                                                            ])
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ak_status') !== 'Masih Hidup'),

                                                        TextInput::make('ak_nomor_handphone')
                                                            ->label('No. Handphone')
                                                            ->helperText('Contoh: 82187782223')
                                                            // ->mask('82187782223')
                                                            ->prefix('62')
                                                            ->tel()
                                                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                                            ->required()
                                                            // ->disabled(fn (Get $get) =>
                                                            // $get('ak_nomor_handphone_sama') === 'Ya')
                                                            ->dehydrated()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ak_tdk_hp') !== 'Ya' ||
                                                                $get('ak_status') !== 'Masih Hidup'),
                                                    ]),

                                                // KARTU KELUARGA AYAH KANDUNG
                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                            <p class="text-lg strong"><strong>KARTU KELUARGA</strong></p>
                                                            <p class="text-lg strong"><strong>AYAH KANDUNG</strong></p>
                                                        </div>'))
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ak_status') !== 'Masih Hidup'),

                                                Grid::make(2)
                                                    ->schema([

                                                        TextInput::make('ak_no_kk')
                                                            ->label('No. KK Ayah Kandung')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->length(16)
                                                            ->required()
                                                            // ->disabled(fn (Get $get) =>
                                                            // $get('ak_kk_sama_pendaftar') === 'Ya')
                                                            ->dehydrated()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ak_status') !== 'Masih Hidup'),

                                                        TextInput::make('ak_kep_kel_kk')
                                                            ->label('Nama Kepala Keluarga')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->required()
                                                            // ->disabled(fn (Get $get) =>
                                                            // $get('ak_kk_sama_pendaftar') === 'Ya')
                                                            ->dehydrated()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ak_status') !== 'Masih Hidup'),
                                                    ]),


                                                // ALAMAT AYAH KANDUNG
                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>TEMPAT TINGGAL DOMISILI</strong></p>
                                                    <p class="text-lg strong"><strong>AYAH KANDUNG</strong></p>
                                                       </div>'))
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ak_status') !== 'Masih Hidup'),

                                                Radio::make('al_ak_tgldi_ln')
                                                    ->label('Apakah tinggal di luar negeri?')
                                                    ->live()
                                                    ->required()
                                                    ->options([
                                                        'Ya' => 'Ya',
                                                        'Tidak' => 'Tidak',
                                                    ])
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ak_status') !== 'Masih Hidup'),

                                                Textarea::make('al_ak_almt_ln')
                                                    ->label('Alamat Luar Negeri')
                                                    ->required()
                                                    ->hidden(fn (Get $get) =>
                                                    $get('al_ak_tgldi_ln') !== 'Ya'),

                                                Select::make('al_ak_stts_rmh')
                                                    ->label('Status Kepemilikan Rumah')
                                                    ->placeholder('Pilih Status Kepemilikan Rumah')
                                                    ->options([
                                                        'Milik Sendiri' => 'Milik Sendiri',
                                                        'Rumah Orang Tua' => 'Rumah Orang Tua',
                                                        'Rumah Saudara/kerabat' => 'Rumah Saudara/kerabat',
                                                        'Rumah Dinas' => 'Rumah Dinas',
                                                        'Sewa/kontrak' => 'Sewa/kontrak',
                                                        'Lainnya' => 'Lainnya',
                                                    ])
                                                    ->searchable()
                                                    ->required()
                                                    ->native(false)
                                                    ->hidden(fn (Get $get) =>
                                                    $get('al_ak_tgldi_ln') !== 'Tidak' ||
                                                        $get('ak_status') !== 'Masih Hidup'),

                                                Grid::make(2)
                                                    ->schema([

                                                        Select::make('al_ak_provinsi_id')
                                                            ->label('Provinsi')
                                                            ->placeholder('Pilih Provinsi')
                                                            ->options(Provinsi::all()->pluck('provinsi', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_ak_tgldi_ln') !== 'Tidak' ||
                                                                $get('ak_status') !== 'Masih Hidup')
                                                            ->afterStateUpdated(function (Set $set) {
                                                                $set('al_ak_kabupaten_id', null);
                                                                $set('al_ak_kecamatan_id', null);
                                                                $set('al_ak_kelurahan_id', null);
                                                                $set('al_ak_kodepos', null);
                                                            }),

                                                        Select::make('al_ak_kabupaten_id')
                                                            ->label('Kabupaten')
                                                            ->placeholder('Pilih Kabupaten')
                                                            ->options(fn (Get $get): Collection => Kabupaten::query()
                                                                ->where('provinsi_id', $get('al_ak_provinsi_id'))
                                                                ->pluck('kabupaten', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_ak_tgldi_ln') !== 'Tidak' ||
                                                                $get('ak_status') !== 'Masih Hidup'),

                                                        Select::make('al_ak_kecamatan_id')
                                                            ->label('Kecamatan')
                                                            ->placeholder('Pilih Kecamatan')
                                                            ->options(fn (Get $get): Collection => Kecamatan::query()
                                                                ->where('kabupaten_id', $get('al_ak_kabupaten_id'))
                                                                ->pluck('kecamatan', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_ak_tgldi_ln') !== 'Tidak' ||
                                                                $get('ak_status') !== 'Masih Hidup'),

                                                        Select::make('al_ak_kelurahan_id')
                                                            ->label('Kelurahan')
                                                            ->placeholder('Pilih Kelurahan')
                                                            ->options(fn (Get $get): Collection => Kelurahan::query()
                                                                ->where('kecamatan_id', $get('al_ak_kecamatan_id'))
                                                                ->pluck('kelurahan', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_ak_tgldi_ln') !== 'Tidak' ||
                                                                $get('ak_status') !== 'Masih Hidup')
                                                            ->afterStateUpdated(function (Get $get, ?string $state, Set $set, ?string $old) {

                                                                if (($get('al_ak_kodepos') ?? '') !== Str::slug($old)) {
                                                                    return;
                                                                }

                                                                $kodepos = Kodepos::where('kelurahan_id', $state)->get('kodepos');

                                                                $state = $kodepos;

                                                                foreach ($state as $state) {
                                                                    $set('al_ak_kodepos', Str::substr($state, 12, 5));
                                                                }
                                                            }),


                                                        TextInput::make('al_ak_rt')
                                                            ->label('RT')
                                                            ->required()
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_ak_tgldi_ln') !== 'Tidak' ||
                                                                $get('ak_status') !== 'Masih Hidup'),

                                                        TextInput::make('al_ak_rw')
                                                            ->label('RW')
                                                            ->required()
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_ak_tgldi_ln') !== 'Tidak' ||
                                                                $get('ak_status') !== 'Masih Hidup'),

                                                        Textarea::make('al_ak_alamat')
                                                            ->label('Alamat')
                                                            ->required()
                                                            ->columnSpanFull()
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_ak_tgldi_ln') !== 'Tidak' ||
                                                                $get('ak_status') !== 'Masih Hidup'),

                                                        TextInput::make('al_ak_kodepos')
                                                            ->label('Kodepos')
                                                            ->disabled()
                                                            ->required()
                                                            ->dehydrated()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_ak_tgldi_ln') !== 'Tidak' ||
                                                                $get('ak_status') !== 'Masih Hidup'),
                                                    ]),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                        <p class="text-lg strong"><strong>Kajian yang diikuti</strong></p>
                                                        </div>'))
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ak_status') !== 'Masih Hidup'),

                                                Textarea::make('ak_ustadz_kajian')
                                                    ->label('Ustadz yang mengisi kajian')
                                                    ->required()
                                                    // ->default('4232')
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ak_status') !== 'Masih Hidup'),

                                                TextArea::make('ak_tempat_kajian')
                                                    ->label('Tempat kajian yang diikuti')
                                                    ->required()
                                                    // ->default('4232')
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ak_status') !== 'Masih Hidup'),





                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b"></div>')),


                                                // //IBU KANDUNG
                                                // Section::make('')
                                                //     ->schema([

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div></div>')),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>IBU KANDUNG</strong></p>
                                                     </div>')),

                                                TextInput::make('ik_nama_lengkap')
                                                    ->label('Nama Lengkap')
                                                    ->hint('Isi sesuai dengan KK')
                                                    ->hintColor('danger')
                                                    ->required()
                                                    // ->disabled(fn (Get $get) =>
                                                    // $get('ik_nama_lengkap_sama') === 'Ya')
                                                    ->dehydrated(),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>STATUS IBU KANDUNG</strong></p>
                                                 </div>')),

                                                Select::make('ik_status')
                                                    ->label('Status')
                                                    ->placeholder('Pilih Status')
                                                    ->options([
                                                        'Masih Hidup' => 'Masih Hidup',
                                                        'Sudah Meninggal' => 'Sudah Meninggal',
                                                        'Tidak Diketahui' => 'Tidak Diketahui',
                                                    ])
                                                    ->required()
                                                    ->live()
                                                    ->native(false),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                     </div>')),

                                                TextInput::make('ik_nama_kunyah')
                                                    ->label('Nama Hijroh/Islami')
                                                    ->required()
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_status') !== 'Masih Hidup'),

                                                Select::make('ik_kewarganegaraan')
                                                    ->label('Kewarganegaraan')
                                                    ->placeholder('Pilih Kewarganegaraan')
                                                    ->options([
                                                        'WNI' => 'WNI',
                                                        'WNA' => 'WNA',
                                                    ])
                                                    ->required()
                                                    ->live()
                                                    ->native(false)
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_status') !== 'Masih Hidup'),

                                                TextInput::make('ik_nik')
                                                    ->label('NIK')
                                                    ->hint('Isi sesuai dengan KK')
                                                    ->hintColor('danger')
                                                    ->length(16)
                                                    ->required()
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_kewarganegaraan') !== 'WNI' ||
                                                        $get('ik_status') !== 'Masih Hidup'),

                                                Grid::make(2)
                                                    ->schema([

                                                        TextInput::make('ik_asal_negara')
                                                            ->label('Asal Negara')
                                                            ->required()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_kewarganegaraan') !== 'WNA' ||
                                                                $get('ik_status') !== 'Masih Hidup'),

                                                        TextInput::make('ik_kitas')
                                                            ->label('KITAS')
                                                            ->hint('Nomor Izin Tinggal (KITAS)')
                                                            ->hintColor('danger')
                                                            ->required()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_kewarganegaraan') !== 'WNA' ||
                                                                $get('ik_status') !== 'Masih Hidup'),
                                                    ]),
                                                Grid::make(2)
                                                    ->schema([

                                                        TextInput::make('ik_tempat_lahir')
                                                            ->label('Tempat Lahir')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->required()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_status') !== 'Masih Hidup'),

                                                        DatePicker::make('ik_tanggal_lahir')
                                                            ->label('Tanggal Lahir')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            // ->helperText('Format: bulan/hari/tahun')
                                                            ->required()
                                                            ->format('d/m/Y')
                                                            ->displayFormat('d/m/Y')
                                                            ->closeOnDateSelection()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_status') !== 'Masih Hidup'),
                                                    ]),

                                                Grid::make(3)
                                                    ->schema([

                                                        Select::make('ik_pend_terakhir')
                                                            ->label('Pendidikan Terakhir')
                                                            ->placeholder('Pilih Pendidikan Terakhir')
                                                            ->options([
                                                                'SD/Sederajat' => 'SD/Sederajat',
                                                                'SMP/Sederajat' => 'SMP/Sederajat',
                                                                'SMA/Sederajat' => 'SMA/Sederajat',
                                                                'D1' => 'D1',
                                                                'D2' => 'D2',
                                                                'D3' => 'D3',
                                                                'D4/S1' => 'D4/S1',
                                                                'S2' => 'S2',
                                                                'S3' => 'S3',
                                                                'Tidak Bersekolah' => 'Tidak Bersekolah',
                                                            ])
                                                            ->searchable()
                                                            ->required()
                                                            ->native(false)
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_status') !== 'Masih Hidup'),

                                                        Select::make('ik_pekerjaan_utama')
                                                            ->label('Pekerjaan Utama')
                                                            ->placeholder('Pilih Pekerjaan Utama')
                                                            ->options([
                                                                'Tidak Bekerja' => 'Tidak Bekerja',
                                                                'Pensiunan' => 'Pensiunan',
                                                                'PNS' => 'PNS',
                                                                'TNI/Polisi' => 'TNI/Polisi',
                                                                'Guru/Dosen' => 'Guru/Dosen',
                                                                'Pegawai Swasta' => 'Pegawai Swasta',
                                                                'Wiraswasta' => 'Wiraswasta',
                                                                'Pengacara/Jaksa/Hakim/Notaris' => 'Pengacara/Jaksa/Hakim/Notaris',
                                                                'Seniman/Pelukis/Artis/Sejenis' => 'Seniman/Pelukis/Artis/Sejenis',
                                                                'Dokter/Bidan/Perawat' => 'Dokter/Bidan/Perawat',
                                                                'Pilot/Pramugara' => 'Pilot/Pramugara',
                                                                'Pedagang' => 'Pedagang',
                                                                'Petani/Peternak' => 'Petani/Peternak',
                                                                'Nelayan' => 'Nelayan',
                                                                'Buruh (Tani/Pabrik/Bangunan)' => 'Buruh (Tani/Pabrik/Bangunan)',
                                                                'Sopir/Masinis/Kondektur' => 'Sopir/Masinis/Kondektur',
                                                                'Politikus' => 'Politikus',
                                                                'Lainnya' => 'Lainnya',
                                                            ])
                                                            ->searchable()
                                                            ->required()
                                                            ->native(false)
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_status') !== 'Masih Hidup'),

                                                        Select::make('ik_pghsln_rt')
                                                            ->label('Penghasilan Rata-Rata')
                                                            ->placeholder('Pilih Penghasilan Rata-Rata')
                                                            ->options([
                                                                'Kurang dari 500.000' => 'Kurang dari 500.000',
                                                                '500.000 - 1.000.000' => '500.000 - 1.000.000',
                                                                '1.000.001 - 2.000.000' => '1.000.001 - 2.000.000',
                                                                '2.000.001 - 3.000.000' => '2.000.001 - 3.000.000',
                                                                '3.000.001 - 5.000.000' => '3.000.001 - 5.000.000',
                                                                'Lebih dari 5.000.000' => 'Lebih dari 5.000.000',
                                                                'Tidak ada' => 'Tidak ada',
                                                            ])
                                                            ->searchable()
                                                            ->required()
                                                            ->native(false)
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_status') !== 'Masih Hidup'),
                                                    ]),

                                                Grid::make(1)
                                                    ->schema([

                                                        Radio::make('ik_tdk_hp')
                                                            ->label('Memiliki nomor handphone?')
                                                            ->live()
                                                            ->required()
                                                            ->options([
                                                                'Ya' => 'Ya',
                                                                'Tidak' => 'Tidak',
                                                            ])
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_status') !== 'Masih Hidup'),

                                                        TextInput::make('ik_nomor_handphone')
                                                            ->label('No. Handphone')
                                                            ->helperText('Contoh: 82187782223')
                                                            // ->mask('82187782223')
                                                            ->prefix('62')
                                                            ->tel()
                                                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                                            ->required()
                                                            // ->disabled(fn (Get $get) =>
                                                            // $get('ik_nomor_handphone_sama') === 'Ya')
                                                            ->dehydrated()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_tdk_hp') !== 'Ya' ||
                                                                $get('ik_status') !== 'Masih Hidup'),
                                                    ]),

                                                // KARTU KELUARGA IBU KANDUNG
                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>KARTU KELUARGA</strong></p>
                                                    <p class="text-lg strong"><strong>IBU KANDUNG</strong></p>
                                                    </div>'))
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_status') !== 'Masih Hidup'),

                                                Radio::make('ik_kk_sama_ak')
                                                    ->label('Apakah KK Ibu Kandung sama dengan KK Ayah Kandung?')
                                                    ->live()
                                                    ->required()
                                                    ->options(function (Get $get) {

                                                        if ($get('ak_status') !== 'Masih Hidup') {

                                                            return ([
                                                                'Tidak' => 'Tidak',
                                                            ]);
                                                        } else {
                                                            return ([
                                                                'Ya' => 'Ya',
                                                                'Tidak' => 'Tidak',
                                                            ]);
                                                        }
                                                    })
                                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                                        $sama = $get('ik_kk_sama_ak');
                                                        $set('al_ik_sama_ak', $sama);

                                                        if ($get('ik_kk_sama_ak') === 'Ya') {
                                                            $set('ik_kk_sama_pendaftar', 'Tidak');
                                                        }
                                                    })
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_status') !== 'Masih Hidup'),

                                                Radio::make('al_ik_sama_ak')
                                                    ->label('Alamat sama dengan Ayah Kandung')
                                                    ->helperText('Untuk mengubah alamat, silakan mengubah status KK Ibu kandung')
                                                    ->disabled()
                                                    ->live()
                                                    ->options([
                                                        'Ya' => 'Ya',
                                                        'Tidak' => 'Tidak',
                                                    ])
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_status') !== 'Masih Hidup'),

                                                Grid::make(2)
                                                    ->schema([

                                                        TextInput::make('ik_no_kk')
                                                            ->label('No. KK Ibu Kandung')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->length(16)
                                                            ->required()
                                                            // ->disabled(fn (Get $get) =>
                                                            // $get('ik_kk_sama_pendaftar') === 'Ya')
                                                            ->dehydrated()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                                $get('ik_status') !== 'Masih Hidup'),

                                                        TextInput::make('ik_kep_kel_kk')
                                                            ->label('Nama Kepala Keluarga')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->required()
                                                            // ->disabled(fn (Get $get) =>
                                                            // $get('ik_kk_sama_pendaftar') === 'Ya')
                                                            ->dehydrated()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                                $get('ik_status') !== 'Masih Hidup'),
                                                    ]),


                                                // ALAMAT AYAH KANDUNG
                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>TEMPAT TINGGAL DOMISILI</strong></p>
                                                    <p class="text-lg strong"><strong>IBU KANDUNG</strong></p>
                                                    </div>'))
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                        $get('ik_status') !== 'Masih Hidup'),

                                                Radio::make('al_ik_tgldi_ln')
                                                    ->label('Apakah tinggal di luar negeri?')
                                                    ->live()
                                                    ->required()
                                                    ->options([
                                                        'Ya' => 'Ya',
                                                        'Tidak' => 'Tidak',
                                                    ])
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                        $get('ik_status') !== 'Masih Hidup'),

                                                Textarea::make('al_ik_almt_ln')
                                                    ->label('Alamat Luar Negeri')
                                                    ->required()
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                        $get('al_ik_tgldi_ln') !== 'Ya' ||
                                                        $get('ik_status') !== 'Masih Hidup'),

                                                Select::make('al_ik_stts_rmh')
                                                    ->label('Status Kepemilikan Rumah')
                                                    ->placeholder('Pilih Status Kepemilikan Rumah')
                                                    ->options([
                                                        'Milik Sendiri' => 'Milik Sendiri',
                                                        'Rumah Orang Tua' => 'Rumah Orang Tua',
                                                        'Rumah Saudara/kerabat' => 'Rumah Saudara/kerabat',
                                                        'Rumah Dinas' => 'Rumah Dinas',
                                                        'Sewa/kontrak' => 'Sewa/kontrak',
                                                        'Lainnya' => 'Lainnya',
                                                    ])
                                                    ->searchable()
                                                    ->required()
                                                    ->native(false)
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                        $get('al_ik_tgldi_ln') !== 'Tidak' ||
                                                        $get('ik_status') !== 'Masih Hidup'),

                                                Grid::make(2)
                                                    ->schema([

                                                        Select::make('al_ik_provinsi_id')
                                                            ->label('Provinsi')
                                                            ->placeholder('Pilih Provinsi')
                                                            ->options(Provinsi::all()->pluck('provinsi', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                                $get('al_ik_tgldi_ln') !== 'Tidak' ||
                                                                $get('ik_status') !== 'Masih Hidup')
                                                            ->afterStateUpdated(function (Set $set) {
                                                                $set('al_ik_kabupaten_id', null);
                                                                $set('al_ik_kecamatan_id', null);
                                                                $set('al_ik_kelurahan_id', null);
                                                                $set('al_ik_kodepos', null);
                                                            }),

                                                        Select::make('al_ik_kabupaten_id')
                                                            ->label('Kabupaten')
                                                            ->placeholder('Pilih Kabupaten')
                                                            ->options(fn (Get $get): Collection => Kabupaten::query()
                                                                ->where('provinsi_id', $get('al_ik_provinsi_id'))
                                                                ->pluck('kabupaten', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                                $get('al_ik_tgldi_ln') !== 'Tidak' ||
                                                                $get('ik_status') !== 'Masih Hidup'),

                                                        Select::make('al_ik_kecamatan_id')
                                                            ->label('Kecamatan')
                                                            ->placeholder('Pilih Kecamatan')
                                                            ->options(fn (Get $get): Collection => Kecamatan::query()
                                                                ->where('kabupaten_id', $get('al_ik_kabupaten_id'))
                                                                ->pluck('kecamatan', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                                $get('al_ik_tgldi_ln') !== 'Tidak' ||
                                                                $get('ik_status') !== 'Masih Hidup'),

                                                        Select::make('al_ik_kelurahan_id')
                                                            ->label('Kelurahan')
                                                            ->placeholder('Pilih Kelurahan')
                                                            ->options(fn (Get $get): Collection => Kelurahan::query()
                                                                ->where('kecamatan_id', $get('al_ik_kecamatan_id'))
                                                                ->pluck('kelurahan', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                                $get('al_ik_tgldi_ln') !== 'Tidak' ||
                                                                $get('ik_status') !== 'Masih Hidup')
                                                            ->afterStateUpdated(function (Get $get, ?string $state, Set $set, ?string $old) {

                                                                if (($get('al_ik_kodepos') ?? '') !== Str::slug($old)) {
                                                                    return;
                                                                }

                                                                $kodepos = Kodepos::where('kelurahan_id', $state)->get('kodepos');

                                                                $state = $kodepos;

                                                                foreach ($state as $state) {
                                                                    $set('al_ik_kodepos', Str::substr($state, 12, 5));
                                                                }
                                                            }),


                                                        TextInput::make('al_ik_rt')
                                                            ->label('RT')
                                                            ->required()
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                                $get('al_ik_tgldi_ln') !== 'Tidak' ||
                                                                $get('ik_status') !== 'Masih Hidup'),

                                                        TextInput::make('al_ik_rw')
                                                            ->label('RW')
                                                            ->required()
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                                $get('al_ik_tgldi_ln') !== 'Tidak' ||
                                                                $get('ik_status') !== 'Masih Hidup'),

                                                        Textarea::make('al_ik_alamat')
                                                            ->label('Alamat')
                                                            ->required()
                                                            ->columnSpanFull()
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                                $get('al_ik_tgldi_ln') !== 'Tidak' ||
                                                                $get('ik_status') !== 'Masih Hidup'),

                                                        TextInput::make('al_ik_kodepos')
                                                            ->label('Kodepos')
                                                            ->disabled()
                                                            ->required()
                                                            ->dehydrated()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                                $get('al_ik_tgldi_ln') !== 'Tidak' ||
                                                                $get('ik_status') !== 'Masih Hidup'),
                                                    ]),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>Kajian yang diikuti</strong></p>
                                                      </div>'))
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_status') !== 'Masih Hidup'),

                                                Textarea::make('ik_ustadz_kajian')
                                                    ->label('Ustadz yang mengisi kajian')
                                                    ->required()
                                                    // ->default('4232')
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_status') !== 'Masih Hidup'),

                                                TextArea::make('ik_tempat_kajian')
                                                    ->label('Tempat kajian yang diikuti')
                                                    ->required()
                                                    // ->default('4232')
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_status') !== 'Masih Hidup'),



                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b border-tsn-accent">
                                                    </div>')),


                                                // //IBU KANDUNG
                                                // Section::make('')
                                                //     ->schema([

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div>
                                                    </div>')),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>WALI</strong></p>
                                                    </div>')),

                                                Select::make('w_status')
                                                    ->label('Status')
                                                    ->placeholder('Pilih Status')
                                                    ->options(function (Get $get) {

                                                        if (($get('ak_status') == "Masih Hidup" && $get('ik_status') == "Masih Hidup")) {
                                                            return ([
                                                                'Sama dengan ayah kandung' => 'Sama dengan ayah kandung',
                                                                'Sama dengan ibu kandung' => 'Sama dengan ibu kandung',
                                                                'Lainnya' => 'Lainnya'
                                                            ]);
                                                        } elseif (($get('ak_status') == "Masih Hidup" && $get('ik_status') !== "Masih Hidup")) {
                                                            return ([
                                                                'Sama dengan ayah kandung' => 'Sama dengan ayah kandung',
                                                                'Lainnya' => 'Lainnya'
                                                            ]);
                                                        } elseif (($get('ak_status') !== "Masih Hidup" && $get('ik_status') == "Masih Hidup")) {
                                                            return ([
                                                                'Sama dengan ibu kandung' => 'Sama dengan ibu kandung',
                                                                'Lainnya' => 'Lainnya'
                                                            ]);
                                                        } elseif (($get('ak_status') !== "Masih Hidup" && $get('ik_status') !== "Masih Hidup")) {
                                                            return ([
                                                                'Lainnya' => 'Lainnya'
                                                            ]);
                                                        }
                                                    })
                                                    ->required()
                                                    ->live()
                                                    ->native(false),

                                                Select::make('w_hubungan')
                                                    ->label('Hubungan wali dengan santri')
                                                    ->placeholder('Pilih Hubungan')
                                                    ->options([
                                                        'Kakek/Nenek' => 'Kakek/Nenek',
                                                        'Paman/Bibi' => 'Paman/Bibi',
                                                        'Kakak' => 'Kakak',
                                                        'Lainnya' => 'Lainnya',
                                                    ])
                                                    ->required()
                                                    ->native(false)
                                                    ->hidden(fn (Get $get) =>
                                                    $get('w_status') !== 'Lainnya'),

                                                TextInput::make('w_nama_lengkap')
                                                    ->label('Nama Lengkap')
                                                    ->hint('Isi sesuai dengan KK')
                                                    ->hintColor('danger')
                                                    ->required()
                                                    // ->disabled(fn (Get $get) =>
                                                    // $get('w_nama_lengkap_sama') === 'Ya')
                                                    ->dehydrated()
                                                    ->hidden(fn (Get $get) =>
                                                    $get('w_status') !== 'Lainnya'),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>STATUS WALI</strong></p>
                                                    </div>'))
                                                    ->hidden(fn (Get $get) =>
                                                    $get('w_status') !== 'Lainnya'),

                                                TextInput::make('w_nama_kunyah')
                                                    ->label('Nama Hijroh/Islami')
                                                    ->required()
                                                    ->hidden(fn (Get $get) =>
                                                    $get('w_status') !== 'Lainnya'),

                                                Select::make('w_kewarganegaraan')
                                                    ->label('Kewarganegaraan')
                                                    ->placeholder('Pilih Kewarganegaraan')
                                                    ->options([
                                                        'WNI' => 'WNI',
                                                        'WNA' => 'WNA',
                                                    ])
                                                    ->required()
                                                    ->live()
                                                    ->native(false)
                                                    ->hidden(fn (Get $get) =>
                                                    $get('w_status') !== 'Lainnya'),

                                                TextInput::make('w_nik')
                                                    ->label('NIK')
                                                    ->hint('Isi sesuai dengan KK')
                                                    ->hintColor('danger')
                                                    ->length(16)
                                                    ->required()
                                                    ->hidden(fn (Get $get) =>
                                                    $get('w_kewarganegaraan') !== 'WNI' ||
                                                        $get('w_status') !== 'Lainnya'),

                                                Grid::make(2)
                                                    ->schema([

                                                        TextInput::make('w_asal_negara')
                                                            ->label('Asal Negara')
                                                            ->required()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('w_kewarganegaraan') !== 'WNA' ||
                                                                $get('w_status') !== 'Lainnya'),

                                                        TextInput::make('w_kitas')
                                                            ->label('KITAS')
                                                            ->hint('Nomor Izin Tinggal (KITAS)')
                                                            ->hintColor('danger')
                                                            ->required()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('w_kewarganegaraan') !== 'WNA' ||
                                                                $get('w_status') !== 'Lainnya'),
                                                    ]),
                                                Grid::make(2)
                                                    ->schema([

                                                        TextInput::make('w_tempat_lahir')
                                                            ->label('Tempat Lahir')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->required()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('w_status') !== 'Lainnya'),

                                                        DatePicker::make('w_tanggal_lahir')
                                                            ->label('Tanggal Lahir')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            // ->helperText('Format: bulan/hari/tahun')
                                                            ->required()
                                                            ->format('d/m/Y')
                                                            ->displayFormat('d/m/Y')
                                                            ->closeOnDateSelection()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('w_status') !== 'Lainnya'),
                                                    ]),

                                                Grid::make(3)
                                                    ->schema([

                                                        Select::make('w_pend_terakhir')
                                                            ->label('Pendidikan Terakhir')
                                                            ->placeholder('Pilih Pendidikan Terakhir')
                                                            ->options([
                                                                'SD/Sederajat' => 'SD/Sederajat',
                                                                'SMP/Sederajat' => 'SMP/Sederajat',
                                                                'SMA/Sederajat' => 'SMA/Sederajat',
                                                                'D1' => 'D1',
                                                                'D2' => 'D2',
                                                                'D3' => 'D3',
                                                                'D4/S1' => 'D4/S1',
                                                                'S2' => 'S2',
                                                                'S3' => 'S3',
                                                                'Tidak Bersekolah' => 'Tidak Bersekolah',
                                                            ])
                                                            ->searchable()
                                                            ->required()
                                                            ->native(false)
                                                            ->hidden(fn (Get $get) =>
                                                            $get('w_status') !== 'Lainnya'),

                                                        Select::make('w_pekerjaan_utama')
                                                            ->label('Pekerjaan Utama')
                                                            ->placeholder('Pilih Pekerjaan Utama')
                                                            ->options([
                                                                'Tidak Bekerja' => 'Tidak Bekerja',
                                                                'Pensiunan' => 'Pensiunan',
                                                                'PNS' => 'PNS',
                                                                'TNI/Polisi' => 'TNI/Polisi',
                                                                'Guru/Dosen' => 'Guru/Dosen',
                                                                'Pegawai Swasta' => 'Pegawai Swasta',
                                                                'Wiraswasta' => 'Wiraswasta',
                                                                'Pengacara/Jaksa/Hakim/Notaris' => 'Pengacara/Jaksa/Hakim/Notaris',
                                                                'Seniman/Pelukis/Artis/Sejenis' => 'Seniman/Pelukis/Artis/Sejenis',
                                                                'Dokter/Bidan/Perawat' => 'Dokter/Bidan/Perawat',
                                                                'Pilot/Pramugara' => 'Pilot/Pramugara',
                                                                'Pedagang' => 'Pedagang',
                                                                'Petani/Peternak' => 'Petani/Peternak',
                                                                'Nelayan' => 'Nelayan',
                                                                'Buruh (Tani/Pabrik/Bangunan)' => 'Buruh (Tani/Pabrik/Bangunan)',
                                                                'Sopir/Masinis/Kondektur' => 'Sopir/Masinis/Kondektur',
                                                                'Politikus' => 'Politikus',
                                                                'Lainnya' => 'Lainnya',
                                                            ])
                                                            ->searchable()
                                                            ->required()
                                                            ->native(false)
                                                            ->hidden(fn (Get $get) =>
                                                            $get('w_status') !== 'Lainnya'),

                                                        Select::make('w_pghsln_rt')
                                                            ->label('Penghasilan Rata-Rata')
                                                            ->placeholder('Pilih Penghasilan Rata-Rata')
                                                            ->options([
                                                                'Kurang dari 500.000' => 'Kurang dari 500.000',
                                                                '500.000 - 1.000.000' => '500.000 - 1.000.000',
                                                                '1.000.001 - 2.000.000' => '1.000.001 - 2.000.000',
                                                                '2.000.001 - 3.000.000' => '2.000.001 - 3.000.000',
                                                                '3.000.001 - 5.000.000' => '3.000.001 - 5.000.000',
                                                                'Lebih dari 5.000.000' => 'Lebih dari 5.000.000',
                                                                'Tidak ada' => 'Tidak ada',
                                                            ])
                                                            ->searchable()
                                                            ->required()
                                                            ->native(false)
                                                            ->hidden(fn (Get $get) =>
                                                            $get('w_status') !== 'Lainnya'),
                                                    ]),

                                                Grid::make(1)
                                                    ->schema([

                                                        Radio::make('w_tdk_hp')
                                                            ->label('Memiliki nomor handphone?')
                                                            ->live()
                                                            ->required()
                                                            ->options([
                                                                'Ya' => 'Ya',
                                                                'Tidak' => 'Tidak',
                                                            ])
                                                            ->hidden(fn (Get $get) =>
                                                            $get('w_status') !== 'Lainnya'),

                                                        TextInput::make('w_nomor_handphone')
                                                            ->label('No. Handphone')
                                                            ->helperText('Contoh: 82187782223')
                                                            // ->mask('82187782223')
                                                            ->prefix('62')
                                                            ->tel()
                                                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                                            ->required()
                                                            // ->disabled(fn (Get $get) =>
                                                            // $get('w_nomor_handphone_sama') === 'Ya')
                                                            ->dehydrated()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('w_tdk_hp') !== 'Ya' ||
                                                                $get('w_status') !== 'Lainnya'),
                                                    ]),

                                                // KARTU KELUARGA WALI
                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                            <p class="text-lg strong"><strong>KARTU KELUARGA</strong></p>
                                                            <p class="text-lg strong"><strong>WALI</strong></p>
                                                        </div>'))
                                                    ->hidden(fn (Get $get) =>
                                                    $get('w_status') !== 'Lainnya'),

                                                Grid::make(2)
                                                    ->schema([

                                                        TextInput::make('w_no_kk')
                                                            ->label('No. KK Wali')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->length(16)
                                                            ->required()
                                                            ->disabled(fn (Get $get) =>
                                                            $get('w_kk_sama_pendaftar') === 'Ya')
                                                            ->dehydrated()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('w_status') !== 'Lainnya'),

                                                        TextInput::make('w_kep_kel_kk')
                                                            ->label('Nama Kepala Keluarga')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->required()
                                                            ->disabled(fn (Get $get) =>
                                                            $get('w_kk_sama_pendaftar') === 'Ya')
                                                            ->dehydrated()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('w_status') !== 'Lainnya'),
                                                    ]),


                                                // ALAMAT WALI
                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>TEMPAT TINGGAL DOMISILI</strong></p>
                                                    <p class="text-lg strong"><strong>WALI</strong></p>
                                                  </div>'))
                                                    ->hidden(fn (Get $get) =>
                                                    $get('w_status') !== 'Lainnya'),

                                                Radio::make('al_w_tgldi_ln')
                                                    ->label('Apakah tinggal di luar negeri?')
                                                    ->live()
                                                    ->required()
                                                    ->options([
                                                        'Ya' => 'Ya',
                                                        'Tidak' => 'Tidak',
                                                    ])
                                                    ->hidden(fn (Get $get) =>
                                                    $get('w_status') !== 'Lainnya'),

                                                Textarea::make('al_w_almt_ln')
                                                    ->label('Alamat Luar Negeri')
                                                    ->required()
                                                    ->hidden(fn (Get $get) =>
                                                    $get('al_w_tgldi_ln') !== 'Ya'),

                                                Select::make('al_w_stts_rmh')
                                                    ->label('Status Kepemilikan Rumah')
                                                    ->placeholder('Pilih Status Kepemilikan Rumah')
                                                    ->options([
                                                        'Milik Sendiri' => 'Milik Sendiri',
                                                        'Rumah Orang Tua' => 'Rumah Orang Tua',
                                                        'Rumah Saudara/kerabat' => 'Rumah Saudara/kerabat',
                                                        'Rumah Dinas' => 'Rumah Dinas',
                                                        'Sewa/kontrak' => 'Sewa/kontrak',
                                                        'Lainnya' => 'Lainnya',
                                                    ])
                                                    ->searchable()
                                                    ->required()
                                                    ->native(false)
                                                    ->hidden(fn (Get $get) =>
                                                    $get('al_w_tgldi_ln') !== 'Tidak' ||
                                                        $get('w_status') !== 'Lainnya'),

                                                Grid::make(2)
                                                    ->schema([

                                                        Select::make('al_w_provinsi_id')
                                                            ->label('Provinsi')
                                                            ->placeholder('Pilih Provinsi')
                                                            ->options(Provinsi::all()->pluck('provinsi', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_w_tgldi_ln') !== 'Tidak' ||
                                                                $get('w_status') !== 'Lainnya')
                                                            ->afterStateUpdated(function (Set $set) {
                                                                $set('al_w_kabupaten_id', null);
                                                                $set('al_w_kecamatan_id', null);
                                                                $set('al_w_kelurahan_id', null);
                                                                $set('al_w_kodepos', null);
                                                            }),

                                                        Select::make('al_w_kabupaten_id')
                                                            ->label('Kabupaten')
                                                            ->placeholder('Pilih Kabupaten')
                                                            ->options(fn (Get $get): Collection => Kabupaten::query()
                                                                ->where('provinsi_id', $get('al_w_provinsi_id'))
                                                                ->pluck('kabupaten', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_w_tgldi_ln') !== 'Tidak' ||
                                                                $get('w_status') !== 'Lainnya'),

                                                        Select::make('al_w_kecamatan_id')
                                                            ->label('Kecamatan')
                                                            ->placeholder('Pilih Kecamatan')
                                                            ->options(fn (Get $get): Collection => Kecamatan::query()
                                                                ->where('kabupaten_id', $get('al_w_kabupaten_id'))
                                                                ->pluck('kecamatan', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_w_tgldi_ln') !== 'Tidak' ||
                                                                $get('w_status') !== 'Lainnya'),

                                                        Select::make('al_w_kelurahan_id')
                                                            ->label('Kelurahan')
                                                            ->placeholder('Pilih Kelurahan')
                                                            ->options(fn (Get $get): Collection => Kelurahan::query()
                                                                ->where('kecamatan_id', $get('al_w_kecamatan_id'))
                                                                ->pluck('kelurahan', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_w_tgldi_ln') !== 'Tidak' ||
                                                                $get('w_status') !== 'Lainnya')
                                                            ->afterStateUpdated(function (Get $get, ?string $state, Set $set, ?string $old) {

                                                                if (($get('al_w_kodepos') ?? '') !== Str::slug($old)) {
                                                                    return;
                                                                }

                                                                $kodepos = Kodepos::where('kelurahan_id', $state)->get('kodepos');

                                                                $state = $kodepos;

                                                                foreach ($state as $state) {
                                                                    $set('al_w_kodepos', Str::substr($state, 12, 5));
                                                                }
                                                            }),


                                                        TextInput::make('al_w_rt')
                                                            ->label('RT')
                                                            ->required()
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_w_tgldi_ln') !== 'Tidak' ||
                                                                $get('w_status') !== 'Lainnya'),

                                                        TextInput::make('al_w_rw')
                                                            ->label('RW')
                                                            ->required()
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_w_tgldi_ln') !== 'Tidak' ||
                                                                $get('w_status') !== 'Lainnya'),

                                                        Textarea::make('al_w_alamat')
                                                            ->label('Alamat')
                                                            ->required()
                                                            ->columnSpanFull()
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_w_tgldi_ln') !== 'Tidak' ||
                                                                $get('w_status') !== 'Lainnya'),

                                                        TextInput::make('al_w_kodepos')
                                                            ->label('Kodepos')
                                                            ->disabled()
                                                            ->required()
                                                            ->dehydrated()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_w_tgldi_ln') !== 'Tidak' ||
                                                                $get('w_status') !== 'Lainnya'),
                                                    ]),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>Kajian yang diikuti</strong></p>
                                                       </div>'))
                                                    ->hidden(fn (Get $get) =>
                                                    $get('w_status') !== 'Lainnya'),

                                                Textarea::make('w_ustadz_kajian')
                                                    ->label('Ustadz yang mengisi kajian')
                                                    ->required()
                                                    // ->default('4232')
                                                    ->hidden(fn (Get $get) =>
                                                    $get('w_status') !== 'Lainnya'),

                                                TextArea::make('w_tempat_kajian')
                                                    ->label('Tempat kajian yang diikuti')
                                                    ->required()
                                                    // ->default('4232')
                                                    ->hidden(fn (Get $get) =>
                                                    $get('w_status') !== 'Lainnya'),



                                                // ->collapsed(fn (Get $get): bool => $get('is_collapse')),

                                                // end of action steps
                                            ])
                                    ]),
                                // end of Walisantri Tab

                                Tabs\Tab::make('Santri')
                                    ->schema([

                                        Group::make()
                                            ->relationship('santri')
                                            ->schema([

                                                //SANTRI
                                                TextInput::make('nama_lengkap')
                                                    ->label('Nama Lengkap')
                                                    ->hint('Isi sesuai dengan KK')
                                                    ->hintColor('danger')
                                                    //->default('asfasdad')
                                                    ->required(),

                                                TextInput::make('nama_panggilan')
                                                    ->label('Nama Hijroh/Islami')
                                                    //->default('asfasdad')
                                                    ->required(),

                                                Select::make('kewarganegaraan')
                                                    ->label('Kewarganegaraan Santri')
                                                    ->placeholder('Pilih Kewarganegaraan')
                                                    ->options([
                                                        'WNI' => 'WNI',
                                                        'WNA' => 'WNA',
                                                    ])
                                                    ->required()
                                                    ->live()
                                                    ->native(false),
                                                //->default('WNI'),

                                                TextInput::make('nik')
                                                    ->label('NIK Santri')
                                                    ->hint('Isi sesuai dengan KK')
                                                    ->hintColor('danger')
                                                    ->length(16)
                                                    ->required()
                                                    ->disabled()
                                                    //->default('3295131306822002')
                                                    ->hidden(fn (Get $get) =>
                                                    $get('kewarganegaraan') !== 'WNI'),

                                                Grid::make(2)
                                                    ->schema([

                                                        TextInput::make('kitas')
                                                            ->label('KITAS Santri')
                                                            ->hint('Nomor Izin Tinggal (KITAS)')
                                                            ->hintColor('danger')
                                                            ->required()
                                                            //->default('3295131306822002')
                                                            ->unique(Santri::class, 'kitas')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('kewarganegaraan') !== 'WNA'),

                                                        TextInput::make('asal_negara')
                                                            ->label('Asal Negara Santri')
                                                            ->required()
                                                            //->default('asfasdad')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('kewarganegaraan') !== 'WNA'),
                                                    ]),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                </div>')),

                                                Grid::make(4)
                                                    ->schema([

                                                        Radio::make('jeniskelamin')
                                                            ->label('Jenis Kelamin')
                                                            ->options([
                                                                'Laki-laki' => 'Laki-laki',
                                                                'Perempuan' => 'Perempuan',
                                                            ])
                                                            ->required()
                                                            //->default('Laki-laki')
                                                            ->inline(),

                                                        TextInput::make('tempat_lahir')
                                                            ->label('Tempat Lahir')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            //->default('asfasdad')
                                                            ->required(),

                                                        DatePicker::make('tanggal_lahir')
                                                            ->label('Tanggal Lahir')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            // ->helperText('Format: bulan/hari/tahun')
                                                            ->required()
                                                            ->format('d/m/Y')
                                                            ->displayFormat('d/m/Y')
                                                            ->closeOnDateSelection()
                                                            ->live()
                                                            ->afterStateUpdated(function (Set $set, $state) {
                                                                $set('umur', Carbon::parse($state)->age);
                                                            }),

                                                        TextInput::make('umur')
                                                            ->label('Umur')
                                                            ->disabled()
                                                            ->dehydrated()
                                                            ->required(),

                                                    ]),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                                Grid::make(2)
                                                    ->schema([

                                                        TextInput::make('anak_ke')
                                                            ->label('Anak ke-')
                                                            ->required()
                                                            //->default('3')
                                                            ->rules([
                                                                fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {

                                                                    $anakke = $get('anak_ke');
                                                                    $psjumlahsaudara = $get('jumlah_saudara');
                                                                    $jumlahsaudara = $psjumlahsaudara + 1;

                                                                    if ($anakke > $jumlahsaudara) {
                                                                        $fail("Anak ke tidak bisa lebih dari jumlah saudara + 1");
                                                                    }
                                                                },
                                                            ]),

                                                        TextInput::make('jumlah_saudara')
                                                            ->label('Jumlah saudara')
                                                            //->default('5')
                                                            ->required(),
                                                    ]),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                                Grid::make(1)
                                                    ->schema([

                                                        TextInput::make('agama')
                                                            ->label('Agama')
                                                            ->default('Islam')
                                                            ->disabled()
                                                            ->required()
                                                            ->dehydrated(),
                                                    ]),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                                Grid::make(2)
                                                    ->schema([

                                                        Select::make('cita_cita')
                                                            ->label('Cita-cita')
                                                            ->placeholder('Pilih Cita-cita')
                                                            ->options([
                                                                'PNS' => 'PNS',
                                                                'TNI/Polri' => 'TNI/Polri',
                                                                'Guru/Dosen' => 'Guru/Dosen',
                                                                'Dokter' => 'Dokter',
                                                                'Politikus' => 'Politikus',
                                                                'Wiraswasta' => 'Wiraswasta',
                                                                'Seniman/Artis' => 'Seniman/Artis',
                                                                'Ilmuwan' => 'Ilmuwan',
                                                                'Agamawan' => 'Agamawan',
                                                                'Lainnya' => 'Lainnya',
                                                            ])
                                                            // ->searchable()
                                                            ->required()
                                                            //->default('Lainnya')
                                                            ->live()
                                                            ->native(false),

                                                        TextInput::make('cita_cita_lainnya')
                                                            ->label('Cita-cita Lainnya')
                                                            ->required()
                                                            //->default('asfasdad')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('cita_cita') !== 'Lainnya'),
                                                    ]),

                                                Grid::make(2)
                                                    ->schema([
                                                        Select::make('hobi')
                                                            ->label('Hobi')
                                                            ->placeholder('Pilih Hobi')
                                                            ->options([
                                                                'Olahraga' => 'Olahraga',
                                                                'Kesenian' => 'Kesenian',
                                                                'Membaca' => 'Membaca',
                                                                'Menulis' => 'Menulis',
                                                                'Jalan-jalan' => 'Jalan-jalan',
                                                                'Lainnya' => 'Lainnya',
                                                            ])
                                                            // ->searchable()
                                                            ->required()
                                                            //->default('Lainnya')
                                                            ->live()
                                                            ->native(false),

                                                        TextInput::make('hobi_lainnya')
                                                            ->label('Hobi Lainnya')
                                                            ->required()
                                                            //->default('asfasdad')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('hobi') !== 'Lainnya'),

                                                    ]),


                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                                Grid::make(2)
                                                    ->schema([
                                                        Select::make('keb_khus')
                                                            ->label('Kebutuhan Khusus')
                                                            ->placeholder('Pilih Kebutuhan Khusus')
                                                            ->options([
                                                                'Tidak Ada' => 'Tidak Ada',
                                                                'Lamban belajar' => 'Lamban belajar',
                                                                'Kesulitan belajar spesifik' => 'Kesulitan belajar spesifik',
                                                                'Gangguan komunikasi' => 'Gangguan komunikasi',
                                                                'Berbakat/memiliki kemampuan dan kecerdasan luar biasa' => 'Berbakat/memiliki kemampuan dan kecerdasan luar biasa',
                                                                'Lainnya' => 'Lainnya',
                                                            ])
                                                            // ->searchable()
                                                            ->required()
                                                            //->default('Lainnya')
                                                            ->live()
                                                            ->native(false),

                                                        TextInput::make('keb_khus_lainnya')
                                                            ->label('Kebutuhan Khusus Lainnya')
                                                            ->required()
                                                            //->default('asfasdad')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('keb_khus') !== 'Lainnya'),
                                                    ]),

                                                Grid::make(2)
                                                    ->schema([
                                                        Select::make('keb_dis')
                                                            ->label('Kebutuhan Disabilitas')
                                                            ->placeholder('Pilih Kebutuhan Disabilitas')
                                                            ->options([
                                                                'Tidak Ada' => 'Tidak Ada',
                                                                'Tuna Netra' => 'Tuna Netra',
                                                                'Tuna Rungu' => 'Tuna Rungu',
                                                                'Tuna Daksa' => 'Tuna Daksa',
                                                                'Tuna Grahita' => 'Tuna Grahita',
                                                                'Tuna Laras' => 'Tuna Laras',
                                                                'Tuna Wicara' => 'Tuna Wicara',
                                                                'Lainnya' => 'Lainnya',
                                                            ])
                                                            // ->searchable()
                                                            ->required()
                                                            //->default('Lainnya')
                                                            ->live()
                                                            ->native(false),

                                                        TextInput::make('keb_dis_lainnya')
                                                            ->label('Kebutuhan Disabilitas Lainnya')
                                                            ->required()
                                                            //->default('asfasdad')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('keb_dis') !== 'Lainnya'),
                                                    ]),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                                Grid::make(1)
                                                    ->schema([

                                                        Radio::make('tdk_hp')
                                                            ->label('Memiliki nomor handphone?')
                                                            ->live()
                                                            ->required()
                                                            //->default('Ya')
                                                            ->options([
                                                                'Ya' => 'Ya',
                                                                'Tidak' => 'Tidak',
                                                            ]),

                                                        TextInput::make('nomor_handphone')
                                                            ->label('No. Handphone')
                                                            ->helperText('Contoh: 82187782223')
                                                            // ->mask('82187782223')
                                                            ->prefix('62')
                                                            ->tel()
                                                            //->default('82187782223')
                                                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                                            ->required()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('tdk_hp') !== 'Ya'),

                                                        TextInput::make('email')
                                                            ->label('Email')
                                                            //->default('mail@mail.com')
                                                            ->email(),
                                                    ]),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                                Grid::make(2)
                                                    ->schema([
                                                        Select::make('bya_sklh')
                                                            ->label('Yang membiayai sekolah')
                                                            ->placeholder('Pilih Yang membiayai sekolah')
                                                            ->options([
                                                                'Orang Tua' => 'Orang Tua',
                                                                'Wali/Orang Tua Asuh' => 'Wali/Orang Tua Asuh',
                                                                'Tanggungan Sendiri' => 'Tanggungan Sendiri',
                                                                'Lainnya' => 'Lainnya',
                                                            ])
                                                            // ->searchable()
                                                            ->required()
                                                            //->default('Lainnya')
                                                            ->live()
                                                            ->native(false),

                                                        TextInput::make('bya_sklh_lainnya')
                                                            ->label('Yang membiayai sekolah lainnya')
                                                            ->required()
                                                            //->default('asfasdad')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('bya_sklh') !== 'Lainnya'),
                                                    ]),

                                                Grid::make(2)
                                                    ->schema([

                                                        Radio::make('belum_nisn')
                                                            ->label('Apakah memiliki NISN?')
                                                            ->helperText(new HtmlString('<strong>NISN</strong> adalah Nomor Induk Siswa Nasional'))
                                                            ->live()
                                                            ->required()
                                                            //->default('Ya')
                                                            ->options([
                                                                'Ya' => 'Ya',
                                                                'Tidak' => 'Tidak',
                                                            ]),

                                                        TextInput::make('nisn')
                                                            ->label('Nomor NISN')
                                                            ->required()
                                                            //->default('2421324')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('belum_nisn') !== 'Ya'),
                                                    ]),

                                                Grid::make(2)
                                                    ->schema([

                                                        Radio::make('nomor_kip_memiliki')
                                                            ->label('Apakah memiliki KIP?')
                                                            ->helperText(new HtmlString('<strong>KIP</strong> adalah Kartu Indonesia Pintar'))
                                                            ->live()
                                                            ->required()
                                                            //->default('Ya')
                                                            ->options([
                                                                'Ya' => 'Ya',
                                                                'Tidak' => 'Tidak',
                                                            ]),

                                                        TextInput::make('nomor_kip')
                                                            ->label('Nomor KIP')
                                                            ->required()
                                                            //->default('32524324')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('nomor_kip_memiliki') !== 'Ya'),
                                                    ]),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                                Grid::make(2)
                                                    ->schema([
                                                        TextInput::make('kartu_keluarga')
                                                            ->label('Nomor KK')
                                                            ->length(16)
                                                            ->required()
                                                            ->disabled()
                                                            ->dehydrated(),

                                                        TextInput::make('nama_kpl_kel')
                                                            ->label('Nama Kepala Keluarga')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->required()
                                                            ->dehydrated(),
                                                    ]),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                                TextInput::make('aktivitaspend')
                                                    ->label('Aktivitas Pendidikan yang Diikuti')
                                                    ->placeholder('Pilih Aktivitas Pendidikan yang Diikuti')
                                                    ->default('PKPPS')
                                                    ->hidden()
                                                    ->dehydrated(),

                                                // ALAMAT SANTRI
                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>TEMPAT TINGGAL DOMISILI</strong></p>
                                                    <p class="text-lg strong"><strong>SANTRI</strong></p>
                                                </div>')),

                                                Radio::make('al_s_status_mukim')
                                                    ->label('Apakah mukim di Pondok?')
                                                    ->helperText(new HtmlString('Pilih <strong>Tidak Mukim</strong> khusus bagi pendaftar <strong>Tarbiyatul Aulaad</strong> dan <strong>Pra Tahfidz kelas 1-4</strong>'))
                                                    ->live()
                                                    ->required()
                                                    //->default('Tidak Mukim')
                                                    ->options([
                                                        'Mukim' => 'Mukim',
                                                        'Tidak Mukim' => 'Tidak Mukim',
                                                    ])
                                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                                        if ($get('al_s_status_mukim') === 'Mukim') {

                                                            $set('al_s_stts_tptgl', 'Tinggal di Asrama Pesantren');
                                                        } elseif ($get('al_s_status_mukim') === 'Tidak Mukim') {

                                                            $set('al_s_stts_tptgl', null);
                                                        }
                                                    }),

                                                Select::make('al_s_stts_tptgl')
                                                    ->label('Status tempat tinggal')
                                                    ->placeholder('Status tempat tinggal')
                                                    ->options([
                                                        'Tinggal dengan Ayah Kandung' => 'Tinggal dengan Ayah Kandung',
                                                        'Tinggal dengan Ibu Kandung' => 'Tinggal dengan Ibu Kandung',
                                                        'Tinggal dengan Wali' => 'Tinggal dengan Wali',
                                                        'Ikut Saudara/Kerabat' => 'Ikut Saudara/Kerabat',
                                                        'Kontrak/Kost' => 'Kontrak/Kost',
                                                        'Tinggal di Asrama Bukan Milik Pesantren' => 'Tinggal di Asrama Bukan Milik Pesantren',
                                                        'Panti Asuhan' => 'Panti Asuhan',
                                                        'Rumah Singgah' => 'Rumah Singgah',
                                                        'Lainnya' => 'Lainnya',
                                                    ])
                                                    // ->searchable()
                                                    ->required()
                                                    //->default('Kontrak/Kost')
                                                    ->live()
                                                    ->native(false)
                                                    ->dehydrated()
                                                    ->hidden(fn (Get $get) =>
                                                    $get('al_s_status_mukim') === 'Mukim'),

                                                Grid::make(2)
                                                    ->schema([

                                                        Select::make('al_s_provinsi_id')
                                                            ->label('Provinsi')
                                                            ->placeholder('Pilih Provinsi')
                                                            ->options(Provinsi::all()->pluck('provinsi', 'id'))
                                                            ->searchable()
                                                            //->default('35')
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(
                                                                fn (Get $get) =>
                                                                $get('al_s_status_mukim') !== 'Tidak Mukim' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ayah Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ibu Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Wali' ||
                                                                    $get('al_s_stts_tptgl') === null
                                                            )
                                                            ->afterStateUpdated(function (Set $set) {
                                                                $set('al_s_kabupaten_id', null);
                                                                $set('al_s_kecamatan_id', null);
                                                                $set('al_s_kelurahan_id', null);
                                                                $set('al_s_kodepos', null);
                                                            }),

                                                        Select::make('al_s_kabupaten_id')
                                                            ->label('Kabupaten')
                                                            ->placeholder('Pilih Kabupaten')
                                                            ->options(fn (Get $get): Collection => Kabupaten::query()
                                                                ->where('provinsi_id', $get('al_s_provinsi_id'))
                                                                ->pluck('kabupaten', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            //->default('232')
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(
                                                                fn (Get $get) =>
                                                                $get('al_s_status_mukim') !== 'Tidak Mukim' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ayah Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ibu Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Wali' ||
                                                                    $get('al_s_stts_tptgl') === null
                                                            ),

                                                        Select::make('al_s_kecamatan_id')
                                                            ->label('Kecamatan')
                                                            ->placeholder('Pilih Kecamatan')
                                                            ->options(fn (Get $get): Collection => Kecamatan::query()
                                                                ->where('kabupaten_id', $get('al_s_kabupaten_id'))
                                                                ->pluck('kecamatan', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            //->default('3617')
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(
                                                                fn (Get $get) =>
                                                                $get('al_s_status_mukim') !== 'Tidak Mukim' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ayah Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ibu Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Wali' ||
                                                                    $get('al_s_stts_tptgl') === null
                                                            ),

                                                        Select::make('al_s_kelurahan_id')
                                                            ->label('Kelurahan')
                                                            ->placeholder('Pilih Kelurahan')
                                                            ->options(fn (Get $get): Collection => Kelurahan::query()
                                                                ->where('kecamatan_id', $get('al_s_kecamatan_id'))
                                                                ->pluck('kelurahan', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            //->default('45322')
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(
                                                                fn (Get $get) =>
                                                                $get('al_s_status_mukim') !== 'Tidak Mukim' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ayah Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ibu Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Wali' ||
                                                                    $get('al_s_stts_tptgl') === null
                                                            )
                                                            ->afterStateUpdated(function (Get $get, ?string $state, Set $set, ?string $old) {

                                                                $kodepos = Kodepos::where('kelurahan_id', $state)->get('kodepos');

                                                                $state = $kodepos;

                                                                foreach ($state as $state) {
                                                                    $set('al_s_kodepos', Str::substr($state, 12, 5));
                                                                }
                                                            }),


                                                        TextInput::make('al_s_rt')
                                                            ->label('RT')
                                                            ->required()
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            //->default('2')
                                                            ->hidden(
                                                                fn (Get $get) =>
                                                                $get('al_s_status_mukim') !== 'Tidak Mukim' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ayah Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ibu Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Wali' ||
                                                                    $get('al_s_stts_tptgl') === null
                                                            ),

                                                        TextInput::make('al_s_rw')
                                                            ->label('RW')
                                                            ->required()
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            //->default('2')
                                                            ->hidden(
                                                                fn (Get $get) =>
                                                                $get('al_s_status_mukim') !== 'Tidak Mukim' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ayah Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ibu Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Wali' ||
                                                                    $get('al_s_stts_tptgl') === null
                                                            ),

                                                        Textarea::make('al_s_alamat')
                                                            ->label('Alamat')
                                                            ->required()
                                                            ->columnSpanFull()
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            //->default('sdfsdasdada')
                                                            ->hidden(
                                                                fn (Get $get) =>
                                                                $get('al_s_status_mukim') !== 'Tidak Mukim' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ayah Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ibu Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Wali' ||
                                                                    $get('al_s_stts_tptgl') === null
                                                            ),

                                                        TextInput::make('al_s_kodepos')
                                                            ->label('Kodepos')
                                                            ->disabled()
                                                            ->required()
                                                            ->dehydrated()
                                                            //->default('63264')
                                                            ->hidden(
                                                                fn (Get $get) =>
                                                                $get('al_s_status_mukim') !== 'Tidak Mukim' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ayah Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ibu Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Wali' ||
                                                                    $get('al_s_stts_tptgl') === null
                                                            ),


                                                        Grid::make(3)
                                                            ->schema([
                                                                Select::make('al_s_jarak')
                                                                    ->label('Jarak tempat tinggal ke Pondok Pesantren')
                                                                    ->options([
                                                                        'Kurang dari 5 km' => 'Kurang dari 5 km',
                                                                        'Antara 5 - 10 Km' => 'Antara 5 - 10 Km',
                                                                        'Antara 11 - 20 Km' => 'Antara 11 - 20 Km',
                                                                        'Antara 21 - 30 Km' => 'Antara 21 - 30 Km',
                                                                        'Lebih dari 30 Km' => 'Lebih dari 30 Km',
                                                                    ])
                                                                    // ->searchable()
                                                                    ->required()
                                                                    //->default('Kurang dari 5 km')
                                                                    ->live()
                                                                    ->native(false)
                                                                    ->hidden(
                                                                        fn (Get $get) =>
                                                                        $get('al_s_status_mukim') !== 'Tidak Mukim' ||
                                                                            $get('al_s_stts_tptgl') === null
                                                                    ),

                                                                Select::make('al_s_transportasi')
                                                                    ->label('Transportasi ke Pondok Pesantren')
                                                                    ->options([
                                                                        'Jalan kaki' => 'Jalan kaki',
                                                                        'Sepeda' => 'Sepeda',
                                                                        'Sepeda Motor' => 'Sepeda Motor',
                                                                        'Mobil Pribadi' => 'Mobil Pribadi',
                                                                        'Antar Jemput Sekolah' => 'Antar Jemput Sekolah',
                                                                        'Angkutan Umum' => 'Angkutan Umum',
                                                                        'Perahu/Sampan' => 'Perahu/Sampan',
                                                                        'Lainnya' => 'Lainnya',
                                                                        'Kendaraan Pribadi' => 'Kendaraan Pribadi',
                                                                        'Kereta Api' => 'Kereta Api',
                                                                        'Ojek' => 'Ojek',
                                                                        'Andong/Bendi/Sado/Dokar/Delman/Becak' => 'Andong/Bendi/Sado/Dokar/Delman/Becak',
                                                                    ])
                                                                    // ->searchable()
                                                                    ->required()
                                                                    //->default('Ojek')
                                                                    ->live()
                                                                    ->native(false)
                                                                    ->hidden(
                                                                        fn (Get $get) =>
                                                                        $get('al_s_status_mukim') !== 'Tidak Mukim' ||
                                                                            $get('al_s_stts_tptgl') === null
                                                                    ),

                                                                Select::make('al_s_waktu_tempuh')
                                                                    ->label('Waktu tempuh ke Pondok Pesantren')
                                                                    ->options([
                                                                        '1 - 10 menit' => '1 - 10 menit',
                                                                        '10 - 19 menit' => '10 - 19 menit',
                                                                        '20 - 29 menit' => '20 - 29 menit',
                                                                        '30 - 39 menit' => '30 - 39 menit',
                                                                        '1 - 2 jam' => '1 - 2 jam',
                                                                        '> 2 jam' => '> 2 jam',
                                                                    ])
                                                                    // ->searchable()
                                                                    ->required()
                                                                    //->default('10 - 19 menit')
                                                                    ->live()
                                                                    ->native(false)
                                                                    ->hidden(
                                                                        fn (Get $get) =>
                                                                        $get('al_s_status_mukim') !== 'Tidak Mukim' ||
                                                                            $get('al_s_stts_tptgl') === null
                                                                    ),

                                                                TextInput::make('al_s_koordinat')
                                                                    ->label('Titik koordinat tempat tinggal')
                                                                    //->default('sfasdadasdads')
                                                                    ->hidden(
                                                                        fn (Get $get) =>
                                                                        $get('al_s_status_mukim') !== 'Tidak Mukim' ||
                                                                            $get('al_s_stts_tptgl') === null
                                                                    )->columnSpanFull(),
                                                            ]),
                                                    ]),

                                                // end of step 2
                                            ])
                                    ]),
                                // end of Santri Tab





                            ])->columnSpanFull()

                    ])
                    ->after(function ($record) {

                        $walisantri = Walisantri::where('id', $record->walisantri_id)->first();
                        $walisantri->ws_emis4 = '1';
                        $walisantri->save();

                        $santri = ModelsSantri::where('id', $record->santri_id)->first();
                        $santri->s_emis4 = '1';
                        $santri->save();

                        Notification::make()
                            ->success()
                            ->title('Alhamdulillah data telah tersimpan')
                            ->persistent()
                            ->color('success')
                            ->send();
                    }),

                Tables\Actions\ViewAction::make()
                    ->label('Lihat Data')
                    ->modalCloseButton(false)
                    ->modalHeading(' ')
                    // ->modalDescription(new HtmlString('<div class="">
                    //                                         <p>Butuh bantuan?</p>
                    //                                         <p>Silakan mengubungi admin di bawah ini:</p>

                    //                                         <table class="table w-fit">
                    //                     <!-- head -->
                    //                     <thead>
                    //                         <tr class="border-tsn-header">
                    //                             <th class="text-tsn-header text-xs" colspan="2"></th>
                    //                         </tr>
                    //                     </thead>
                    //                     <tbody>
                    //                         <!-- row 1 -->
                    //                         <tr>
                    //                             <th><a href="https://wa.me/6282210862400"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    //                             <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
                    //                             </svg>
                    //                             </a></th>
                    //                             <td class="text-xs"><a href="https://wa.me/6282210862400">WA Admin Putra (Abu Hammaam)</a></td>
                    //                         </tr>
                    //                         <tr>
                    //                             <th><a href="https://wa.me/6285236459012"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    //                             <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
                    //                             </svg>
                    //                             </a></th>
                    //                             <td class="text-xs"><a href="https://wa.me/6285236459012">WA Admin Putra (Abu Fathimah Hendi)</a></td>
                    //                         </tr>
                    //                         <tr>
                    //                             <th><a href="https://wa.me/6281333838691"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    //                             <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
                    //                             </svg>
                    //                             </a></th>
                    //                             <td class="text-xs"><a href="https://wa.me/6281333838691">WA Admin Putra (Akh Irfan)</a></td>
                    //                         </tr>
                    //                         <tr>
                    //                             <th><a href="https://wa.me/628175765767"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    //                             <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
                    //                             </svg>
                    //                             </a></th>
                    //                             <td class="text-xs"><a href="https://wa.me/628175765767">WA Admin Putri</a></td>
                    //                         </tr>


                    //                     </tbody>
                    //                     </table>

                    //                                     </div>'))
                    ->modalWidth('full')
                    // ->stickyModalHeader()
                    ->button()
                    ->closeModalByClickingAway(false)
                    ->modalCancelAction(fn (StaticAction $action) => $action->label('Tutup'))
                    ->form([

                        Section::make()
                            ->schema([

                                Placeholder::make('')
                                    ->content(function (Model $record) {
                                        $santri = ModelsSantri::where('id', $record->santri_id)->first();
                                        return (new HtmlString('<div><p class="text-3xl"><strong>' . $santri->nama_lengkap . '</strong></p></div>'));
                                    }),

                                Placeholder::make('')
                                    ->content(function (Model $record) {
                                        $santri = ModelsSantri::where('id', $record->santri_id)->first();
                                        $tahunberjalanaktif = TahunBerjalan::where('is_active', 1)->first();
                                        $ts = TahunBerjalan::where('tb', $tahunberjalanaktif->ts)->first();

                                        $cekdatats = KelasSantri::where('tahun_berjalan_id', $ts->id)
                                            ->where('santri_id', $record->santri_id)->count();

                                        if ($cekdatats !== 0) {
                                            $tahunberjalanaktif = TahunBerjalan::where('is_active', 1)->first();
                                            $ts = TahunBerjalan::where('tb', $tahunberjalanaktif->ts)->first();

                                            $cekdatats = KelasSantri::where('tahun_berjalan_id', $ts->id)
                                                ->where('santri_id', $record->santri_id)->first();

                                            $abbrqism = Qism::where('id', $cekdatats->qism_id)->first();

                                            $abbrkelas = Kelas::where('id', $cekdatats->kelas_id)->first();


                                            return (new HtmlString('<div class="">
                                            <table class="table w-fit">
                        <!-- head -->
                        <thead>
                            <tr class="border-tsn-header">
                                <th class="text-tsn-header text-xl" colspan="3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- row 1 -->
                            <tr>
                                <th class="text-xl">Qism</th>
                                <td class="text-xl">:</td>
                                <td class="text-xl">' . $abbrqism->qism . '</td>
                            </tr>
                            <tr>
                                <th class="text-xl">Kelas</th>
                                <td class="text-xl">:</td>
                                <td class="text-xl">' . $abbrkelas->kelas . '</td>
                            </tr>
                            <tr>
                                <th class="text-xl">NISM</th>
                                <td class="text-xl">:</td>
                                <td class="text-xl">' . $santri->nism . '</td>
                            </tr>



                        </tbody>
                        </table>

                                        </div>'));
                                        } elseif ($cekdatats === 0) {
                                            $tahunberjalanaktif = TahunBerjalan::where('is_active', 1)->first();
                                            $ts = TahunBerjalan::where('tb', $tahunberjalanaktif->ts)->first();

                                            $cekdatats = KelasSantri::where('tahun_berjalan_id', $tahunberjalanaktif->id)
                                                ->where('santri_id', $record->santri_id)->first();

                                            $abbrqism = Qism::where('id', $cekdatats->qism_id)->first();

                                            $abbrkelas = Kelas::where('id', $cekdatats->kelas_id)->first();




                                            return (new HtmlString('<div class="">
                                            <table class="table w-fit">
                        <!-- head -->
                        <thead>
                            <tr class="border-tsn-header">
                                <th class="text-tsn-header text-xl" colspan="3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- row 1 -->
                            <tr>
                                <th class="text-xl">Qism</th>
                                <td class="text-xl">:</td>
                                <td class="text-xl">' . $abbrqism->qism . '</td>
                            </tr>
                            <tr>
                                <th class="text-xl">Kelas</th>
                                <td class="text-xl">:</td>
                                <td class="text-xl">' . $abbrkelas->kelas . '</td>
                            </tr>
                            <tr>
                                <th class="text-xl">NISM</th>
                                <td class="text-xl">:</td>
                                <td class="text-xl">' . $santri->nism . '</td>
                            </tr>



                        </tbody>
                        </table>

                                        </div>'));
                                        }
                                    }),

                            ]),

                        Tabs::make('Tabs')
                            ->tabs([

                                Tabs\Tab::make('Walisantri')
                                    ->schema([

                                        Group::make()
                                            ->relationship('walisantri')
                                            ->schema([

                                                //AYAH KANDUNG
                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b"><p class="text-lg strong"><strong>AYAH KANDUNG</strong></p></div>')),

                                                TextInput::make('ak_nama_lengkap')
                                                    ->label('Nama Lengkap')
                                                    ->hint('Isi sesuai dengan KK')
                                                    ->hintColor('danger')
                                                    ->required()
                                                    // ->disabled(fn (Get $get) =>
                                                    // $get('ak_nama_lengkap_sama') === 'Ya')
                                                    ->dehydrated(),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>STATUS AYAH KANDUNG</strong></p>
                                                       </div>')),

                                                Select::make('ak_status')
                                                    ->label('Status')
                                                    ->placeholder('Pilih Status')
                                                    ->options([
                                                        'Masih Hidup' => 'Masih Hidup',
                                                        'Sudah Meninggal' => 'Sudah Meninggal',
                                                        'Tidak Diketahui' => 'Tidak Diketahui',
                                                    ])
                                                    ->required()
                                                    ->live()
                                                    ->native(false),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                       </div>')),

                                                TextInput::make('ak_nama_kunyah')
                                                    ->label('Nama Hijroh/Islami')
                                                    ->required()
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ak_status') !== 'Masih Hidup'),

                                                Select::make('ak_kewarganegaraan')
                                                    ->label('Kewarganegaraan')
                                                    ->placeholder('Pilih Kewarganegaraan')
                                                    ->options([
                                                        'WNI' => 'WNI',
                                                        'WNA' => 'WNA',
                                                    ])
                                                    ->required()
                                                    ->live()
                                                    ->native(false)
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ak_status') !== 'Masih Hidup'),

                                                TextInput::make('ak_nik')
                                                    ->label('NIK')
                                                    ->hint('Isi sesuai dengan KK')
                                                    ->hintColor('danger')
                                                    ->length(16)
                                                    ->required()
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ak_kewarganegaraan') !== 'WNI' ||
                                                        $get('ak_status') !== 'Masih Hidup'),

                                                Grid::make(2)
                                                    ->schema([

                                                        TextInput::make('ak_asal_negara')
                                                            ->label('Asal Negara')
                                                            ->required()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ak_kewarganegaraan') !== 'WNA' ||
                                                                $get('ak_status') !== 'Masih Hidup'),

                                                        TextInput::make('ak_kitas')
                                                            ->label('KITAS')
                                                            ->hint('Nomor Izin Tinggal (KITAS)')
                                                            ->hintColor('danger')
                                                            ->required()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ak_kewarganegaraan') !== 'WNA' ||
                                                                $get('ak_status') !== 'Masih Hidup'),
                                                    ]),
                                                Grid::make(2)
                                                    ->schema([

                                                        TextInput::make('ak_tempat_lahir')
                                                            ->label('Tempat Lahir')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->required()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ak_status') !== 'Masih Hidup'),

                                                        DatePicker::make('ak_tanggal_lahir')
                                                            ->label('Tanggal Lahir')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            // ->helperText('Format: bulan/hari/tahun')
                                                            ->required()
                                                            ->format('d/m/Y')
                                                            ->displayFormat('d/m/Y')
                                                            ->closeOnDateSelection()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ak_status') !== 'Masih Hidup'),
                                                    ]),

                                                Grid::make(3)
                                                    ->schema([

                                                        Select::make('ak_pend_terakhir')
                                                            ->label('Pendidikan Terakhir')
                                                            ->placeholder('Pilih Pendidikan Terakhir')
                                                            ->options([
                                                                'SD/Sederajat' => 'SD/Sederajat',
                                                                'SMP/Sederajat' => 'SMP/Sederajat',
                                                                'SMA/Sederajat' => 'SMA/Sederajat',
                                                                'D1' => 'D1',
                                                                'D2' => 'D2',
                                                                'D3' => 'D3',
                                                                'D4/S1' => 'D4/S1',
                                                                'S2' => 'S2',
                                                                'S3' => 'S3',
                                                                'Tidak Bersekolah' => 'Tidak Bersekolah',
                                                            ])
                                                            ->searchable()
                                                            ->required()
                                                            ->native(false)
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ak_status') !== 'Masih Hidup'),

                                                        Select::make('ak_pekerjaan_utama')
                                                            ->label('Pekerjaan Utama')
                                                            ->placeholder('Pilih Pekerjaan Utama')
                                                            ->options([
                                                                'Tidak Bekerja' => 'Tidak Bekerja',
                                                                'Pensiunan' => 'Pensiunan',
                                                                'PNS' => 'PNS',
                                                                'TNI/Polisi' => 'TNI/Polisi',
                                                                'Guru/Dosen' => 'Guru/Dosen',
                                                                'Pegawai Swasta' => 'Pegawai Swasta',
                                                                'Wiraswasta' => 'Wiraswasta',
                                                                'Pengacara/Jaksa/Hakim/Notaris' => 'Pengacara/Jaksa/Hakim/Notaris',
                                                                'Seniman/Pelukis/Artis/Sejenis' => 'Seniman/Pelukis/Artis/Sejenis',
                                                                'Dokter/Bidan/Perawat' => 'Dokter/Bidan/Perawat',
                                                                'Pilot/Pramugara' => 'Pilot/Pramugara',
                                                                'Pedagang' => 'Pedagang',
                                                                'Petani/Peternak' => 'Petani/Peternak',
                                                                'Nelayan' => 'Nelayan',
                                                                'Buruh (Tani/Pabrik/Bangunan)' => 'Buruh (Tani/Pabrik/Bangunan)',
                                                                'Sopir/Masinis/Kondektur' => 'Sopir/Masinis/Kondektur',
                                                                'Politikus' => 'Politikus',
                                                                'Lainnya' => 'Lainnya',
                                                            ])
                                                            ->searchable()
                                                            ->required()
                                                            ->native(false)
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ak_status') !== 'Masih Hidup'),

                                                        Select::make('ak_pghsln_rt')
                                                            ->label('Penghasilan Rata-Rata')
                                                            ->placeholder('Pilih Penghasilan Rata-Rata')
                                                            ->options([
                                                                'Kurang dari 500.000' => 'Kurang dari 500.000',
                                                                '500.000 - 1.000.000' => '500.000 - 1.000.000',
                                                                '1.000.001 - 2.000.000' => '1.000.001 - 2.000.000',
                                                                '2.000.001 - 3.000.000' => '2.000.001 - 3.000.000',
                                                                '3.000.001 - 5.000.000' => '3.000.001 - 5.000.000',
                                                                'Lebih dari 5.000.000' => 'Lebih dari 5.000.000',
                                                                'Tidak ada' => 'Tidak ada',
                                                            ])
                                                            ->searchable()
                                                            ->required()
                                                            ->native(false)
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ak_status') !== 'Masih Hidup'),
                                                    ]),

                                                Grid::make(1)
                                                    ->schema([

                                                        Radio::make('ak_tdk_hp')
                                                            ->label('Memiliki nomor handphone?')
                                                            ->live()
                                                            ->required()
                                                            ->options([
                                                                'Ya' => 'Ya',
                                                                'Tidak' => 'Tidak',
                                                            ])
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ak_status') !== 'Masih Hidup'),

                                                        TextInput::make('ak_nomor_handphone')
                                                            ->label('No. Handphone')
                                                            ->helperText('Contoh: 82187782223')
                                                            // ->mask('82187782223')
                                                            ->prefix('62')
                                                            ->tel()
                                                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                                            ->required()
                                                            // ->disabled(fn (Get $get) =>
                                                            // $get('ak_nomor_handphone_sama') === 'Ya')
                                                            ->dehydrated()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ak_tdk_hp') !== 'Ya' ||
                                                                $get('ak_status') !== 'Masih Hidup'),
                                                    ]),

                                                // KARTU KELUARGA AYAH KANDUNG
                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                            <p class="text-lg strong"><strong>KARTU KELUARGA</strong></p>
                                                            <p class="text-lg strong"><strong>AYAH KANDUNG</strong></p>
                                                        </div>'))
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ak_status') !== 'Masih Hidup'),

                                                Grid::make(2)
                                                    ->schema([

                                                        TextInput::make('ak_no_kk')
                                                            ->label('No. KK Ayah Kandung')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->length(16)
                                                            ->required()
                                                            // ->disabled(fn (Get $get) =>
                                                            // $get('ak_kk_sama_pendaftar') === 'Ya')
                                                            ->dehydrated()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ak_status') !== 'Masih Hidup'),

                                                        TextInput::make('ak_kep_kel_kk')
                                                            ->label('Nama Kepala Keluarga')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->required()
                                                            // ->disabled(fn (Get $get) =>
                                                            // $get('ak_kk_sama_pendaftar') === 'Ya')
                                                            ->dehydrated()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ak_status') !== 'Masih Hidup'),
                                                    ]),


                                                // ALAMAT AYAH KANDUNG
                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>TEMPAT TINGGAL DOMISILI</strong></p>
                                                    <p class="text-lg strong"><strong>AYAH KANDUNG</strong></p>
                                                       </div>'))
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ak_status') !== 'Masih Hidup'),

                                                Radio::make('al_ak_tgldi_ln')
                                                    ->label('Apakah tinggal di luar negeri?')
                                                    ->live()
                                                    ->required()
                                                    ->options([
                                                        'Ya' => 'Ya',
                                                        'Tidak' => 'Tidak',
                                                    ])
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ak_status') !== 'Masih Hidup'),

                                                Textarea::make('al_ak_almt_ln')
                                                    ->label('Alamat Luar Negeri')
                                                    ->required()
                                                    ->hidden(fn (Get $get) =>
                                                    $get('al_ak_tgldi_ln') !== 'Ya'),

                                                Select::make('al_ak_stts_rmh')
                                                    ->label('Status Kepemilikan Rumah')
                                                    ->placeholder('Pilih Status Kepemilikan Rumah')
                                                    ->options([
                                                        'Milik Sendiri' => 'Milik Sendiri',
                                                        'Rumah Orang Tua' => 'Rumah Orang Tua',
                                                        'Rumah Saudara/kerabat' => 'Rumah Saudara/kerabat',
                                                        'Rumah Dinas' => 'Rumah Dinas',
                                                        'Sewa/kontrak' => 'Sewa/kontrak',
                                                        'Lainnya' => 'Lainnya',
                                                    ])
                                                    ->searchable()
                                                    ->required()
                                                    ->native(false)
                                                    ->hidden(fn (Get $get) =>
                                                    $get('al_ak_tgldi_ln') !== 'Tidak' ||
                                                        $get('ak_status') !== 'Masih Hidup'),

                                                Grid::make(2)
                                                    ->schema([

                                                        Select::make('al_ak_provinsi_id')
                                                            ->label('Provinsi')
                                                            ->placeholder('Pilih Provinsi')
                                                            ->options(Provinsi::all()->pluck('provinsi', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_ak_tgldi_ln') !== 'Tidak' ||
                                                                $get('ak_status') !== 'Masih Hidup')
                                                            ->afterStateUpdated(function (Set $set) {
                                                                $set('al_ak_kabupaten_id', null);
                                                                $set('al_ak_kecamatan_id', null);
                                                                $set('al_ak_kelurahan_id', null);
                                                                $set('al_ak_kodepos', null);
                                                            }),

                                                        Select::make('al_ak_kabupaten_id')
                                                            ->label('Kabupaten')
                                                            ->placeholder('Pilih Kabupaten')
                                                            ->options(fn (Get $get): Collection => Kabupaten::query()
                                                                ->where('provinsi_id', $get('al_ak_provinsi_id'))
                                                                ->pluck('kabupaten', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_ak_tgldi_ln') !== 'Tidak' ||
                                                                $get('ak_status') !== 'Masih Hidup'),

                                                        Select::make('al_ak_kecamatan_id')
                                                            ->label('Kecamatan')
                                                            ->placeholder('Pilih Kecamatan')
                                                            ->options(fn (Get $get): Collection => Kecamatan::query()
                                                                ->where('kabupaten_id', $get('al_ak_kabupaten_id'))
                                                                ->pluck('kecamatan', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_ak_tgldi_ln') !== 'Tidak' ||
                                                                $get('ak_status') !== 'Masih Hidup'),

                                                        Select::make('al_ak_kelurahan_id')
                                                            ->label('Kelurahan')
                                                            ->placeholder('Pilih Kelurahan')
                                                            ->options(fn (Get $get): Collection => Kelurahan::query()
                                                                ->where('kecamatan_id', $get('al_ak_kecamatan_id'))
                                                                ->pluck('kelurahan', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_ak_tgldi_ln') !== 'Tidak' ||
                                                                $get('ak_status') !== 'Masih Hidup')
                                                            ->afterStateUpdated(function (Get $get, ?string $state, Set $set, ?string $old) {

                                                                if (($get('al_ak_kodepos') ?? '') !== Str::slug($old)) {
                                                                    return;
                                                                }

                                                                $kodepos = Kodepos::where('kelurahan_id', $state)->get('kodepos');

                                                                $state = $kodepos;

                                                                foreach ($state as $state) {
                                                                    $set('al_ak_kodepos', Str::substr($state, 12, 5));
                                                                }
                                                            }),


                                                        TextInput::make('al_ak_rt')
                                                            ->label('RT')
                                                            ->required()
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_ak_tgldi_ln') !== 'Tidak' ||
                                                                $get('ak_status') !== 'Masih Hidup'),

                                                        TextInput::make('al_ak_rw')
                                                            ->label('RW')
                                                            ->required()
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_ak_tgldi_ln') !== 'Tidak' ||
                                                                $get('ak_status') !== 'Masih Hidup'),

                                                        Textarea::make('al_ak_alamat')
                                                            ->label('Alamat')
                                                            ->required()
                                                            ->columnSpanFull()
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_ak_tgldi_ln') !== 'Tidak' ||
                                                                $get('ak_status') !== 'Masih Hidup'),

                                                        TextInput::make('al_ak_kodepos')
                                                            ->label('Kodepos')
                                                            ->disabled()
                                                            ->required()
                                                            ->dehydrated()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_ak_tgldi_ln') !== 'Tidak' ||
                                                                $get('ak_status') !== 'Masih Hidup'),
                                                    ]),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                        <p class="text-lg strong"><strong>Kajian yang diikuti</strong></p>
                                                        </div>'))
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ak_status') !== 'Masih Hidup'),

                                                Textarea::make('ak_ustadz_kajian')
                                                    ->label('Ustadz yang mengisi kajian')
                                                    ->required()
                                                    // ->default('4232')
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ak_status') !== 'Masih Hidup'),

                                                TextArea::make('ak_tempat_kajian')
                                                    ->label('Tempat kajian yang diikuti')
                                                    ->required()
                                                    // ->default('4232')
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ak_status') !== 'Masih Hidup'),





                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b"></div>')),


                                                // //IBU KANDUNG
                                                // Section::make('')
                                                //     ->schema([

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div></div>')),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>IBU KANDUNG</strong></p>
                                                     </div>')),

                                                TextInput::make('ik_nama_lengkap')
                                                    ->label('Nama Lengkap')
                                                    ->hint('Isi sesuai dengan KK')
                                                    ->hintColor('danger')
                                                    ->required()
                                                    // ->disabled(fn (Get $get) =>
                                                    // $get('ik_nama_lengkap_sama') === 'Ya')
                                                    ->dehydrated(),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>STATUS IBU KANDUNG</strong></p>
                                                 </div>')),

                                                Select::make('ik_status')
                                                    ->label('Status')
                                                    ->placeholder('Pilih Status')
                                                    ->options([
                                                        'Masih Hidup' => 'Masih Hidup',
                                                        'Sudah Meninggal' => 'Sudah Meninggal',
                                                        'Tidak Diketahui' => 'Tidak Diketahui',
                                                    ])
                                                    ->required()
                                                    ->live()
                                                    ->native(false),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                     </div>')),

                                                TextInput::make('ik_nama_kunyah')
                                                    ->label('Nama Hijroh/Islami')
                                                    ->required()
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_status') !== 'Masih Hidup'),

                                                Select::make('ik_kewarganegaraan')
                                                    ->label('Kewarganegaraan')
                                                    ->placeholder('Pilih Kewarganegaraan')
                                                    ->options([
                                                        'WNI' => 'WNI',
                                                        'WNA' => 'WNA',
                                                    ])
                                                    ->required()
                                                    ->live()
                                                    ->native(false)
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_status') !== 'Masih Hidup'),

                                                TextInput::make('ik_nik')
                                                    ->label('NIK')
                                                    ->hint('Isi sesuai dengan KK')
                                                    ->hintColor('danger')
                                                    ->length(16)
                                                    ->required()
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_kewarganegaraan') !== 'WNI' ||
                                                        $get('ik_status') !== 'Masih Hidup'),

                                                Grid::make(2)
                                                    ->schema([

                                                        TextInput::make('ik_asal_negara')
                                                            ->label('Asal Negara')
                                                            ->required()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_kewarganegaraan') !== 'WNA' ||
                                                                $get('ik_status') !== 'Masih Hidup'),

                                                        TextInput::make('ik_kitas')
                                                            ->label('KITAS')
                                                            ->hint('Nomor Izin Tinggal (KITAS)')
                                                            ->hintColor('danger')
                                                            ->required()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_kewarganegaraan') !== 'WNA' ||
                                                                $get('ik_status') !== 'Masih Hidup'),
                                                    ]),
                                                Grid::make(2)
                                                    ->schema([

                                                        TextInput::make('ik_tempat_lahir')
                                                            ->label('Tempat Lahir')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->required()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_status') !== 'Masih Hidup'),

                                                        DatePicker::make('ik_tanggal_lahir')
                                                            ->label('Tanggal Lahir')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            // ->helperText('Format: bulan/hari/tahun')
                                                            ->required()
                                                            ->format('d/m/Y')
                                                            ->displayFormat('d/m/Y')
                                                            ->closeOnDateSelection()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_status') !== 'Masih Hidup'),
                                                    ]),

                                                Grid::make(3)
                                                    ->schema([

                                                        Select::make('ik_pend_terakhir')
                                                            ->label('Pendidikan Terakhir')
                                                            ->placeholder('Pilih Pendidikan Terakhir')
                                                            ->options([
                                                                'SD/Sederajat' => 'SD/Sederajat',
                                                                'SMP/Sederajat' => 'SMP/Sederajat',
                                                                'SMA/Sederajat' => 'SMA/Sederajat',
                                                                'D1' => 'D1',
                                                                'D2' => 'D2',
                                                                'D3' => 'D3',
                                                                'D4/S1' => 'D4/S1',
                                                                'S2' => 'S2',
                                                                'S3' => 'S3',
                                                                'Tidak Bersekolah' => 'Tidak Bersekolah',
                                                            ])
                                                            ->searchable()
                                                            ->required()
                                                            ->native(false)
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_status') !== 'Masih Hidup'),

                                                        Select::make('ik_pekerjaan_utama')
                                                            ->label('Pekerjaan Utama')
                                                            ->placeholder('Pilih Pekerjaan Utama')
                                                            ->options([
                                                                'Tidak Bekerja' => 'Tidak Bekerja',
                                                                'Pensiunan' => 'Pensiunan',
                                                                'PNS' => 'PNS',
                                                                'TNI/Polisi' => 'TNI/Polisi',
                                                                'Guru/Dosen' => 'Guru/Dosen',
                                                                'Pegawai Swasta' => 'Pegawai Swasta',
                                                                'Wiraswasta' => 'Wiraswasta',
                                                                'Pengacara/Jaksa/Hakim/Notaris' => 'Pengacara/Jaksa/Hakim/Notaris',
                                                                'Seniman/Pelukis/Artis/Sejenis' => 'Seniman/Pelukis/Artis/Sejenis',
                                                                'Dokter/Bidan/Perawat' => 'Dokter/Bidan/Perawat',
                                                                'Pilot/Pramugara' => 'Pilot/Pramugara',
                                                                'Pedagang' => 'Pedagang',
                                                                'Petani/Peternak' => 'Petani/Peternak',
                                                                'Nelayan' => 'Nelayan',
                                                                'Buruh (Tani/Pabrik/Bangunan)' => 'Buruh (Tani/Pabrik/Bangunan)',
                                                                'Sopir/Masinis/Kondektur' => 'Sopir/Masinis/Kondektur',
                                                                'Politikus' => 'Politikus',
                                                                'Lainnya' => 'Lainnya',
                                                            ])
                                                            ->searchable()
                                                            ->required()
                                                            ->native(false)
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_status') !== 'Masih Hidup'),

                                                        Select::make('ik_pghsln_rt')
                                                            ->label('Penghasilan Rata-Rata')
                                                            ->placeholder('Pilih Penghasilan Rata-Rata')
                                                            ->options([
                                                                'Kurang dari 500.000' => 'Kurang dari 500.000',
                                                                '500.000 - 1.000.000' => '500.000 - 1.000.000',
                                                                '1.000.001 - 2.000.000' => '1.000.001 - 2.000.000',
                                                                '2.000.001 - 3.000.000' => '2.000.001 - 3.000.000',
                                                                '3.000.001 - 5.000.000' => '3.000.001 - 5.000.000',
                                                                'Lebih dari 5.000.000' => 'Lebih dari 5.000.000',
                                                                'Tidak ada' => 'Tidak ada',
                                                            ])
                                                            ->searchable()
                                                            ->required()
                                                            ->native(false)
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_status') !== 'Masih Hidup'),
                                                    ]),

                                                Grid::make(1)
                                                    ->schema([

                                                        Radio::make('ik_tdk_hp')
                                                            ->label('Memiliki nomor handphone?')
                                                            ->live()
                                                            ->required()
                                                            ->options([
                                                                'Ya' => 'Ya',
                                                                'Tidak' => 'Tidak',
                                                            ])
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_status') !== 'Masih Hidup'),

                                                        TextInput::make('ik_nomor_handphone')
                                                            ->label('No. Handphone')
                                                            ->helperText('Contoh: 82187782223')
                                                            // ->mask('82187782223')
                                                            ->prefix('62')
                                                            ->tel()
                                                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                                            ->required()
                                                            // ->disabled(fn (Get $get) =>
                                                            // $get('ik_nomor_handphone_sama') === 'Ya')
                                                            ->dehydrated()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_tdk_hp') !== 'Ya' ||
                                                                $get('ik_status') !== 'Masih Hidup'),
                                                    ]),

                                                // KARTU KELUARGA IBU KANDUNG
                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>KARTU KELUARGA</strong></p>
                                                    <p class="text-lg strong"><strong>IBU KANDUNG</strong></p>
                                                    </div>'))
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_status') !== 'Masih Hidup'),

                                                Radio::make('ik_kk_sama_ak')
                                                    ->label('Apakah KK Ibu Kandung sama dengan KK Ayah Kandung?')
                                                    ->live()
                                                    ->required()
                                                    ->options(function (Get $get) {

                                                        if ($get('ak_status') !== 'Masih Hidup') {

                                                            return ([
                                                                'Tidak' => 'Tidak',
                                                            ]);
                                                        } else {
                                                            return ([
                                                                'Ya' => 'Ya',
                                                                'Tidak' => 'Tidak',
                                                            ]);
                                                        }
                                                    })
                                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                                        $sama = $get('ik_kk_sama_ak');
                                                        $set('al_ik_sama_ak', $sama);

                                                        if ($get('ik_kk_sama_ak') === 'Ya') {
                                                            $set('ik_kk_sama_pendaftar', 'Tidak');
                                                        }
                                                    })
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_status') !== 'Masih Hidup'),

                                                Radio::make('al_ik_sama_ak')
                                                    ->label('Alamat sama dengan Ayah Kandung')
                                                    ->helperText('Untuk mengubah alamat, silakan mengubah status KK Ibu kandung')
                                                    ->disabled()
                                                    ->live()
                                                    ->options([
                                                        'Ya' => 'Ya',
                                                        'Tidak' => 'Tidak',
                                                    ])
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_status') !== 'Masih Hidup'),

                                                Grid::make(2)
                                                    ->schema([

                                                        TextInput::make('ik_no_kk')
                                                            ->label('No. KK Ibu Kandung')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->length(16)
                                                            ->required()
                                                            // ->disabled(fn (Get $get) =>
                                                            // $get('ik_kk_sama_pendaftar') === 'Ya')
                                                            ->dehydrated()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                                $get('ik_status') !== 'Masih Hidup'),

                                                        TextInput::make('ik_kep_kel_kk')
                                                            ->label('Nama Kepala Keluarga')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->required()
                                                            // ->disabled(fn (Get $get) =>
                                                            // $get('ik_kk_sama_pendaftar') === 'Ya')
                                                            ->dehydrated()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                                $get('ik_status') !== 'Masih Hidup'),
                                                    ]),


                                                // ALAMAT AYAH KANDUNG
                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>TEMPAT TINGGAL DOMISILI</strong></p>
                                                    <p class="text-lg strong"><strong>IBU KANDUNG</strong></p>
                                                    </div>'))
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                        $get('ik_status') !== 'Masih Hidup'),

                                                Radio::make('al_ik_tgldi_ln')
                                                    ->label('Apakah tinggal di luar negeri?')
                                                    ->live()
                                                    ->required()
                                                    ->options([
                                                        'Ya' => 'Ya',
                                                        'Tidak' => 'Tidak',
                                                    ])
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                        $get('ik_status') !== 'Masih Hidup'),

                                                Textarea::make('al_ik_almt_ln')
                                                    ->label('Alamat Luar Negeri')
                                                    ->required()
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                        $get('al_ik_tgldi_ln') !== 'Ya' ||
                                                        $get('ik_status') !== 'Masih Hidup'),

                                                Select::make('al_ik_stts_rmh')
                                                    ->label('Status Kepemilikan Rumah')
                                                    ->placeholder('Pilih Status Kepemilikan Rumah')
                                                    ->options([
                                                        'Milik Sendiri' => 'Milik Sendiri',
                                                        'Rumah Orang Tua' => 'Rumah Orang Tua',
                                                        'Rumah Saudara/kerabat' => 'Rumah Saudara/kerabat',
                                                        'Rumah Dinas' => 'Rumah Dinas',
                                                        'Sewa/kontrak' => 'Sewa/kontrak',
                                                        'Lainnya' => 'Lainnya',
                                                    ])
                                                    ->searchable()
                                                    ->required()
                                                    ->native(false)
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                        $get('al_ik_tgldi_ln') !== 'Tidak' ||
                                                        $get('ik_status') !== 'Masih Hidup'),

                                                Grid::make(2)
                                                    ->schema([

                                                        Select::make('al_ik_provinsi_id')
                                                            ->label('Provinsi')
                                                            ->placeholder('Pilih Provinsi')
                                                            ->options(Provinsi::all()->pluck('provinsi', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                                $get('al_ik_tgldi_ln') !== 'Tidak' ||
                                                                $get('ik_status') !== 'Masih Hidup')
                                                            ->afterStateUpdated(function (Set $set) {
                                                                $set('al_ik_kabupaten_id', null);
                                                                $set('al_ik_kecamatan_id', null);
                                                                $set('al_ik_kelurahan_id', null);
                                                                $set('al_ik_kodepos', null);
                                                            }),

                                                        Select::make('al_ik_kabupaten_id')
                                                            ->label('Kabupaten')
                                                            ->placeholder('Pilih Kabupaten')
                                                            ->options(fn (Get $get): Collection => Kabupaten::query()
                                                                ->where('provinsi_id', $get('al_ik_provinsi_id'))
                                                                ->pluck('kabupaten', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                                $get('al_ik_tgldi_ln') !== 'Tidak' ||
                                                                $get('ik_status') !== 'Masih Hidup'),

                                                        Select::make('al_ik_kecamatan_id')
                                                            ->label('Kecamatan')
                                                            ->placeholder('Pilih Kecamatan')
                                                            ->options(fn (Get $get): Collection => Kecamatan::query()
                                                                ->where('kabupaten_id', $get('al_ik_kabupaten_id'))
                                                                ->pluck('kecamatan', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                                $get('al_ik_tgldi_ln') !== 'Tidak' ||
                                                                $get('ik_status') !== 'Masih Hidup'),

                                                        Select::make('al_ik_kelurahan_id')
                                                            ->label('Kelurahan')
                                                            ->placeholder('Pilih Kelurahan')
                                                            ->options(fn (Get $get): Collection => Kelurahan::query()
                                                                ->where('kecamatan_id', $get('al_ik_kecamatan_id'))
                                                                ->pluck('kelurahan', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                                $get('al_ik_tgldi_ln') !== 'Tidak' ||
                                                                $get('ik_status') !== 'Masih Hidup')
                                                            ->afterStateUpdated(function (Get $get, ?string $state, Set $set, ?string $old) {

                                                                if (($get('al_ik_kodepos') ?? '') !== Str::slug($old)) {
                                                                    return;
                                                                }

                                                                $kodepos = Kodepos::where('kelurahan_id', $state)->get('kodepos');

                                                                $state = $kodepos;

                                                                foreach ($state as $state) {
                                                                    $set('al_ik_kodepos', Str::substr($state, 12, 5));
                                                                }
                                                            }),


                                                        TextInput::make('al_ik_rt')
                                                            ->label('RT')
                                                            ->required()
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                                $get('al_ik_tgldi_ln') !== 'Tidak' ||
                                                                $get('ik_status') !== 'Masih Hidup'),

                                                        TextInput::make('al_ik_rw')
                                                            ->label('RW')
                                                            ->required()
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                                $get('al_ik_tgldi_ln') !== 'Tidak' ||
                                                                $get('ik_status') !== 'Masih Hidup'),

                                                        Textarea::make('al_ik_alamat')
                                                            ->label('Alamat')
                                                            ->required()
                                                            ->columnSpanFull()
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                                $get('al_ik_tgldi_ln') !== 'Tidak' ||
                                                                $get('ik_status') !== 'Masih Hidup'),

                                                        TextInput::make('al_ik_kodepos')
                                                            ->label('Kodepos')
                                                            ->disabled()
                                                            ->required()
                                                            ->dehydrated()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('ik_kk_sama_ak') !== 'Tidak' ||
                                                                $get('al_ik_tgldi_ln') !== 'Tidak' ||
                                                                $get('ik_status') !== 'Masih Hidup'),
                                                    ]),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>Kajian yang diikuti</strong></p>
                                                      </div>'))
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_status') !== 'Masih Hidup'),

                                                Textarea::make('ik_ustadz_kajian')
                                                    ->label('Ustadz yang mengisi kajian')
                                                    ->required()
                                                    // ->default('4232')
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_status') !== 'Masih Hidup'),

                                                TextArea::make('ik_tempat_kajian')
                                                    ->label('Tempat kajian yang diikuti')
                                                    ->required()
                                                    // ->default('4232')
                                                    ->hidden(fn (Get $get) =>
                                                    $get('ik_status') !== 'Masih Hidup'),



                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b border-tsn-accent">
                                                    </div>')),


                                                // //IBU KANDUNG
                                                // Section::make('')
                                                //     ->schema([

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div>
                                                    </div>')),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>WALI</strong></p>
                                                    </div>')),

                                                Select::make('w_status')
                                                    ->label('Status')
                                                    ->placeholder('Pilih Status')
                                                    ->options(function (Get $get) {

                                                        if (($get('ak_status') == "Masih Hidup" && $get('ik_status') == "Masih Hidup")) {
                                                            return ([
                                                                'Sama dengan ayah kandung' => 'Sama dengan ayah kandung',
                                                                'Sama dengan ibu kandung' => 'Sama dengan ibu kandung',
                                                                'Lainnya' => 'Lainnya'
                                                            ]);
                                                        } elseif (($get('ak_status') == "Masih Hidup" && $get('ik_status') !== "Masih Hidup")) {
                                                            return ([
                                                                'Sama dengan ayah kandung' => 'Sama dengan ayah kandung',
                                                                'Lainnya' => 'Lainnya'
                                                            ]);
                                                        } elseif (($get('ak_status') !== "Masih Hidup" && $get('ik_status') == "Masih Hidup")) {
                                                            return ([
                                                                'Sama dengan ibu kandung' => 'Sama dengan ibu kandung',
                                                                'Lainnya' => 'Lainnya'
                                                            ]);
                                                        } elseif (($get('ak_status') !== "Masih Hidup" && $get('ik_status') !== "Masih Hidup")) {
                                                            return ([
                                                                'Lainnya' => 'Lainnya'
                                                            ]);
                                                        }
                                                    })
                                                    ->required()
                                                    ->live()
                                                    ->native(false),

                                                Select::make('w_hubungan')
                                                    ->label('Hubungan wali dengan santri')
                                                    ->placeholder('Pilih Hubungan')
                                                    ->options([
                                                        'Kakek/Nenek' => 'Kakek/Nenek',
                                                        'Paman/Bibi' => 'Paman/Bibi',
                                                        'Kakak' => 'Kakak',
                                                        'Lainnya' => 'Lainnya',
                                                    ])
                                                    ->required()
                                                    ->native(false)
                                                    ->hidden(fn (Get $get) =>
                                                    $get('w_status') !== 'Lainnya'),

                                                TextInput::make('w_nama_lengkap')
                                                    ->label('Nama Lengkap')
                                                    ->hint('Isi sesuai dengan KK')
                                                    ->hintColor('danger')
                                                    ->required()
                                                    // ->disabled(fn (Get $get) =>
                                                    // $get('w_nama_lengkap_sama') === 'Ya')
                                                    ->dehydrated()
                                                    ->hidden(fn (Get $get) =>
                                                    $get('w_status') !== 'Lainnya'),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>STATUS WALI</strong></p>
                                                    </div>'))
                                                    ->hidden(fn (Get $get) =>
                                                    $get('w_status') !== 'Lainnya'),

                                                TextInput::make('w_nama_kunyah')
                                                    ->label('Nama Hijroh/Islami')
                                                    ->required()
                                                    ->hidden(fn (Get $get) =>
                                                    $get('w_status') !== 'Lainnya'),

                                                Select::make('w_kewarganegaraan')
                                                    ->label('Kewarganegaraan')
                                                    ->placeholder('Pilih Kewarganegaraan')
                                                    ->options([
                                                        'WNI' => 'WNI',
                                                        'WNA' => 'WNA',
                                                    ])
                                                    ->required()
                                                    ->live()
                                                    ->native(false)
                                                    ->hidden(fn (Get $get) =>
                                                    $get('w_status') !== 'Lainnya'),

                                                TextInput::make('w_nik')
                                                    ->label('NIK')
                                                    ->hint('Isi sesuai dengan KK')
                                                    ->hintColor('danger')
                                                    ->length(16)
                                                    ->required()
                                                    ->hidden(fn (Get $get) =>
                                                    $get('w_kewarganegaraan') !== 'WNI' ||
                                                        $get('w_status') !== 'Lainnya'),

                                                Grid::make(2)
                                                    ->schema([

                                                        TextInput::make('w_asal_negara')
                                                            ->label('Asal Negara')
                                                            ->required()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('w_kewarganegaraan') !== 'WNA' ||
                                                                $get('w_status') !== 'Lainnya'),

                                                        TextInput::make('w_kitas')
                                                            ->label('KITAS')
                                                            ->hint('Nomor Izin Tinggal (KITAS)')
                                                            ->hintColor('danger')
                                                            ->required()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('w_kewarganegaraan') !== 'WNA' ||
                                                                $get('w_status') !== 'Lainnya'),
                                                    ]),
                                                Grid::make(2)
                                                    ->schema([

                                                        TextInput::make('w_tempat_lahir')
                                                            ->label('Tempat Lahir')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->required()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('w_status') !== 'Lainnya'),

                                                        DatePicker::make('w_tanggal_lahir')
                                                            ->label('Tanggal Lahir')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            // ->helperText('Format: bulan/hari/tahun')
                                                            ->required()
                                                            ->format('d/m/Y')
                                                            ->displayFormat('d/m/Y')
                                                            ->closeOnDateSelection()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('w_status') !== 'Lainnya'),
                                                    ]),

                                                Grid::make(3)
                                                    ->schema([

                                                        Select::make('w_pend_terakhir')
                                                            ->label('Pendidikan Terakhir')
                                                            ->placeholder('Pilih Pendidikan Terakhir')
                                                            ->options([
                                                                'SD/Sederajat' => 'SD/Sederajat',
                                                                'SMP/Sederajat' => 'SMP/Sederajat',
                                                                'SMA/Sederajat' => 'SMA/Sederajat',
                                                                'D1' => 'D1',
                                                                'D2' => 'D2',
                                                                'D3' => 'D3',
                                                                'D4/S1' => 'D4/S1',
                                                                'S2' => 'S2',
                                                                'S3' => 'S3',
                                                                'Tidak Bersekolah' => 'Tidak Bersekolah',
                                                            ])
                                                            ->searchable()
                                                            ->required()
                                                            ->native(false)
                                                            ->hidden(fn (Get $get) =>
                                                            $get('w_status') !== 'Lainnya'),

                                                        Select::make('w_pekerjaan_utama')
                                                            ->label('Pekerjaan Utama')
                                                            ->placeholder('Pilih Pekerjaan Utama')
                                                            ->options([
                                                                'Tidak Bekerja' => 'Tidak Bekerja',
                                                                'Pensiunan' => 'Pensiunan',
                                                                'PNS' => 'PNS',
                                                                'TNI/Polisi' => 'TNI/Polisi',
                                                                'Guru/Dosen' => 'Guru/Dosen',
                                                                'Pegawai Swasta' => 'Pegawai Swasta',
                                                                'Wiraswasta' => 'Wiraswasta',
                                                                'Pengacara/Jaksa/Hakim/Notaris' => 'Pengacara/Jaksa/Hakim/Notaris',
                                                                'Seniman/Pelukis/Artis/Sejenis' => 'Seniman/Pelukis/Artis/Sejenis',
                                                                'Dokter/Bidan/Perawat' => 'Dokter/Bidan/Perawat',
                                                                'Pilot/Pramugara' => 'Pilot/Pramugara',
                                                                'Pedagang' => 'Pedagang',
                                                                'Petani/Peternak' => 'Petani/Peternak',
                                                                'Nelayan' => 'Nelayan',
                                                                'Buruh (Tani/Pabrik/Bangunan)' => 'Buruh (Tani/Pabrik/Bangunan)',
                                                                'Sopir/Masinis/Kondektur' => 'Sopir/Masinis/Kondektur',
                                                                'Politikus' => 'Politikus',
                                                                'Lainnya' => 'Lainnya',
                                                            ])
                                                            ->searchable()
                                                            ->required()
                                                            ->native(false)
                                                            ->hidden(fn (Get $get) =>
                                                            $get('w_status') !== 'Lainnya'),

                                                        Select::make('w_pghsln_rt')
                                                            ->label('Penghasilan Rata-Rata')
                                                            ->placeholder('Pilih Penghasilan Rata-Rata')
                                                            ->options([
                                                                'Kurang dari 500.000' => 'Kurang dari 500.000',
                                                                '500.000 - 1.000.000' => '500.000 - 1.000.000',
                                                                '1.000.001 - 2.000.000' => '1.000.001 - 2.000.000',
                                                                '2.000.001 - 3.000.000' => '2.000.001 - 3.000.000',
                                                                '3.000.001 - 5.000.000' => '3.000.001 - 5.000.000',
                                                                'Lebih dari 5.000.000' => 'Lebih dari 5.000.000',
                                                                'Tidak ada' => 'Tidak ada',
                                                            ])
                                                            ->searchable()
                                                            ->required()
                                                            ->native(false)
                                                            ->hidden(fn (Get $get) =>
                                                            $get('w_status') !== 'Lainnya'),
                                                    ]),

                                                Grid::make(1)
                                                    ->schema([

                                                        Radio::make('w_tdk_hp')
                                                            ->label('Memiliki nomor handphone?')
                                                            ->live()
                                                            ->required()
                                                            ->options([
                                                                'Ya' => 'Ya',
                                                                'Tidak' => 'Tidak',
                                                            ])
                                                            ->hidden(fn (Get $get) =>
                                                            $get('w_status') !== 'Lainnya'),

                                                        TextInput::make('w_nomor_handphone')
                                                            ->label('No. Handphone')
                                                            ->helperText('Contoh: 82187782223')
                                                            // ->mask('82187782223')
                                                            ->prefix('62')
                                                            ->tel()
                                                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                                            ->required()
                                                            // ->disabled(fn (Get $get) =>
                                                            // $get('w_nomor_handphone_sama') === 'Ya')
                                                            ->dehydrated()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('w_tdk_hp') !== 'Ya' ||
                                                                $get('w_status') !== 'Lainnya'),
                                                    ]),

                                                // KARTU KELUARGA WALI
                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                            <p class="text-lg strong"><strong>KARTU KELUARGA</strong></p>
                                                            <p class="text-lg strong"><strong>WALI</strong></p>
                                                        </div>'))
                                                    ->hidden(fn (Get $get) =>
                                                    $get('w_status') !== 'Lainnya'),

                                                Grid::make(2)
                                                    ->schema([

                                                        TextInput::make('w_no_kk')
                                                            ->label('No. KK Wali')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->length(16)
                                                            ->required()
                                                            ->disabled(fn (Get $get) =>
                                                            $get('w_kk_sama_pendaftar') === 'Ya')
                                                            ->dehydrated()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('w_status') !== 'Lainnya'),

                                                        TextInput::make('w_kep_kel_kk')
                                                            ->label('Nama Kepala Keluarga')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->required()
                                                            ->disabled(fn (Get $get) =>
                                                            $get('w_kk_sama_pendaftar') === 'Ya')
                                                            ->dehydrated()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('w_status') !== 'Lainnya'),
                                                    ]),


                                                // ALAMAT WALI
                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>TEMPAT TINGGAL DOMISILI</strong></p>
                                                    <p class="text-lg strong"><strong>WALI</strong></p>
                                                  </div>'))
                                                    ->hidden(fn (Get $get) =>
                                                    $get('w_status') !== 'Lainnya'),

                                                Radio::make('al_w_tgldi_ln')
                                                    ->label('Apakah tinggal di luar negeri?')
                                                    ->live()
                                                    ->required()
                                                    ->options([
                                                        'Ya' => 'Ya',
                                                        'Tidak' => 'Tidak',
                                                    ])
                                                    ->hidden(fn (Get $get) =>
                                                    $get('w_status') !== 'Lainnya'),

                                                Textarea::make('al_w_almt_ln')
                                                    ->label('Alamat Luar Negeri')
                                                    ->required()
                                                    ->hidden(fn (Get $get) =>
                                                    $get('al_w_tgldi_ln') !== 'Ya'),

                                                Select::make('al_w_stts_rmh')
                                                    ->label('Status Kepemilikan Rumah')
                                                    ->placeholder('Pilih Status Kepemilikan Rumah')
                                                    ->options([
                                                        'Milik Sendiri' => 'Milik Sendiri',
                                                        'Rumah Orang Tua' => 'Rumah Orang Tua',
                                                        'Rumah Saudara/kerabat' => 'Rumah Saudara/kerabat',
                                                        'Rumah Dinas' => 'Rumah Dinas',
                                                        'Sewa/kontrak' => 'Sewa/kontrak',
                                                        'Lainnya' => 'Lainnya',
                                                    ])
                                                    ->searchable()
                                                    ->required()
                                                    ->native(false)
                                                    ->hidden(fn (Get $get) =>
                                                    $get('al_w_tgldi_ln') !== 'Tidak' ||
                                                        $get('w_status') !== 'Lainnya'),

                                                Grid::make(2)
                                                    ->schema([

                                                        Select::make('al_w_provinsi_id')
                                                            ->label('Provinsi')
                                                            ->placeholder('Pilih Provinsi')
                                                            ->options(Provinsi::all()->pluck('provinsi', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_w_tgldi_ln') !== 'Tidak' ||
                                                                $get('w_status') !== 'Lainnya')
                                                            ->afterStateUpdated(function (Set $set) {
                                                                $set('al_w_kabupaten_id', null);
                                                                $set('al_w_kecamatan_id', null);
                                                                $set('al_w_kelurahan_id', null);
                                                                $set('al_w_kodepos', null);
                                                            }),

                                                        Select::make('al_w_kabupaten_id')
                                                            ->label('Kabupaten')
                                                            ->placeholder('Pilih Kabupaten')
                                                            ->options(fn (Get $get): Collection => Kabupaten::query()
                                                                ->where('provinsi_id', $get('al_w_provinsi_id'))
                                                                ->pluck('kabupaten', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_w_tgldi_ln') !== 'Tidak' ||
                                                                $get('w_status') !== 'Lainnya'),

                                                        Select::make('al_w_kecamatan_id')
                                                            ->label('Kecamatan')
                                                            ->placeholder('Pilih Kecamatan')
                                                            ->options(fn (Get $get): Collection => Kecamatan::query()
                                                                ->where('kabupaten_id', $get('al_w_kabupaten_id'))
                                                                ->pluck('kecamatan', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_w_tgldi_ln') !== 'Tidak' ||
                                                                $get('w_status') !== 'Lainnya'),

                                                        Select::make('al_w_kelurahan_id')
                                                            ->label('Kelurahan')
                                                            ->placeholder('Pilih Kelurahan')
                                                            ->options(fn (Get $get): Collection => Kelurahan::query()
                                                                ->where('kecamatan_id', $get('al_w_kecamatan_id'))
                                                                ->pluck('kelurahan', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_w_tgldi_ln') !== 'Tidak' ||
                                                                $get('w_status') !== 'Lainnya')
                                                            ->afterStateUpdated(function (Get $get, ?string $state, Set $set, ?string $old) {

                                                                if (($get('al_w_kodepos') ?? '') !== Str::slug($old)) {
                                                                    return;
                                                                }

                                                                $kodepos = Kodepos::where('kelurahan_id', $state)->get('kodepos');

                                                                $state = $kodepos;

                                                                foreach ($state as $state) {
                                                                    $set('al_w_kodepos', Str::substr($state, 12, 5));
                                                                }
                                                            }),


                                                        TextInput::make('al_w_rt')
                                                            ->label('RT')
                                                            ->required()
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_w_tgldi_ln') !== 'Tidak' ||
                                                                $get('w_status') !== 'Lainnya'),

                                                        TextInput::make('al_w_rw')
                                                            ->label('RW')
                                                            ->required()
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_w_tgldi_ln') !== 'Tidak' ||
                                                                $get('w_status') !== 'Lainnya'),

                                                        Textarea::make('al_w_alamat')
                                                            ->label('Alamat')
                                                            ->required()
                                                            ->columnSpanFull()
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_w_tgldi_ln') !== 'Tidak' ||
                                                                $get('w_status') !== 'Lainnya'),

                                                        TextInput::make('al_w_kodepos')
                                                            ->label('Kodepos')
                                                            ->disabled()
                                                            ->required()
                                                            ->dehydrated()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('al_w_tgldi_ln') !== 'Tidak' ||
                                                                $get('w_status') !== 'Lainnya'),
                                                    ]),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>Kajian yang diikuti</strong></p>
                                                       </div>'))
                                                    ->hidden(fn (Get $get) =>
                                                    $get('w_status') !== 'Lainnya'),

                                                Textarea::make('w_ustadz_kajian')
                                                    ->label('Ustadz yang mengisi kajian')
                                                    ->required()
                                                    // ->default('4232')
                                                    ->hidden(fn (Get $get) =>
                                                    $get('w_status') !== 'Lainnya'),

                                                TextArea::make('w_tempat_kajian')
                                                    ->label('Tempat kajian yang diikuti')
                                                    ->required()
                                                    // ->default('4232')
                                                    ->hidden(fn (Get $get) =>
                                                    $get('w_status') !== 'Lainnya'),



                                                // ->collapsed(fn (Get $get): bool => $get('is_collapse')),

                                                // end of action steps
                                            ])
                                    ]),
                                // end of Walisantri Tab

                                Tabs\Tab::make('Santri')
                                    ->schema([

                                        Group::make()
                                            ->relationship('santri')
                                            ->schema([

                                                //SANTRI
                                                TextInput::make('nama_lengkap')
                                                    ->label('Nama Lengkap')
                                                    ->hint('Isi sesuai dengan KK')
                                                    ->hintColor('danger')
                                                    //->default('asfasdad')
                                                    ->required(),

                                                TextInput::make('nama_panggilan')
                                                    ->label('Nama Hijroh/Islami')
                                                    //->default('asfasdad')
                                                    ->required(),

                                                Select::make('kewarganegaraan')
                                                    ->label('Kewarganegaraan Santri')
                                                    ->placeholder('Pilih Kewarganegaraan')
                                                    ->options([
                                                        'WNI' => 'WNI',
                                                        'WNA' => 'WNA',
                                                    ])
                                                    ->required()
                                                    ->live()
                                                    ->native(false),
                                                //->default('WNI'),

                                                TextInput::make('nik')
                                                    ->label('NIK Santri')
                                                    ->hint('Isi sesuai dengan KK')
                                                    ->hintColor('danger')
                                                    ->length(16)
                                                    ->required()
                                                    ->disabled()
                                                    //->default('3295131306822002')
                                                    ->hidden(fn (Get $get) =>
                                                    $get('kewarganegaraan') !== 'WNI'),

                                                Grid::make(2)
                                                    ->schema([

                                                        TextInput::make('kitas')
                                                            ->label('KITAS Santri')
                                                            ->hint('Nomor Izin Tinggal (KITAS)')
                                                            ->hintColor('danger')
                                                            ->required()
                                                            //->default('3295131306822002')
                                                            ->unique(Santri::class, 'kitas')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('kewarganegaraan') !== 'WNA'),

                                                        TextInput::make('asal_negara')
                                                            ->label('Asal Negara Santri')
                                                            ->required()
                                                            //->default('asfasdad')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('kewarganegaraan') !== 'WNA'),
                                                    ]),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                </div>')),

                                                Grid::make(4)
                                                    ->schema([

                                                        Radio::make('jeniskelamin')
                                                            ->label('Jenis Kelamin')
                                                            ->options([
                                                                'Laki-laki' => 'Laki-laki',
                                                                'Perempuan' => 'Perempuan',
                                                            ])
                                                            ->required()
                                                            //->default('Laki-laki')
                                                            ->inline(),

                                                        TextInput::make('tempat_lahir')
                                                            ->label('Tempat Lahir')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            //->default('asfasdad')
                                                            ->required(),

                                                        DatePicker::make('tanggal_lahir')
                                                            ->label('Tanggal Lahir')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            // ->helperText('Format: bulan/hari/tahun')
                                                            ->required()
                                                            ->format('d/m/Y')
                                                            ->displayFormat('d/m/Y')
                                                            ->closeOnDateSelection()
                                                            ->live()
                                                            ->afterStateUpdated(function (Set $set, $state) {
                                                                $set('umur', Carbon::parse($state)->age);
                                                            }),

                                                        TextInput::make('umur')
                                                            ->label('Umur')
                                                            ->disabled()
                                                            ->dehydrated()
                                                            ->required(),

                                                    ]),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                                Grid::make(2)
                                                    ->schema([

                                                        TextInput::make('anak_ke')
                                                            ->label('Anak ke-')
                                                            ->required()
                                                            //->default('3')
                                                            ->rules([
                                                                fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {

                                                                    $anakke = $get('anak_ke');
                                                                    $psjumlahsaudara = $get('jumlah_saudara');
                                                                    $jumlahsaudara = $psjumlahsaudara + 1;

                                                                    if ($anakke > $jumlahsaudara) {
                                                                        $fail("Anak ke tidak bisa lebih dari jumlah saudara + 1");
                                                                    }
                                                                },
                                                            ]),

                                                        TextInput::make('jumlah_saudara')
                                                            ->label('Jumlah saudara')
                                                            //->default('5')
                                                            ->required(),
                                                    ]),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                                Grid::make(1)
                                                    ->schema([

                                                        TextInput::make('agama')
                                                            ->label('Agama')
                                                            ->default('Islam')
                                                            ->disabled()
                                                            ->required()
                                                            ->dehydrated(),
                                                    ]),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                                Grid::make(2)
                                                    ->schema([

                                                        Select::make('cita_cita')
                                                            ->label('Cita-cita')
                                                            ->placeholder('Pilih Cita-cita')
                                                            ->options([
                                                                'PNS' => 'PNS',
                                                                'TNI/Polri' => 'TNI/Polri',
                                                                'Guru/Dosen' => 'Guru/Dosen',
                                                                'Dokter' => 'Dokter',
                                                                'Politikus' => 'Politikus',
                                                                'Wiraswasta' => 'Wiraswasta',
                                                                'Seniman/Artis' => 'Seniman/Artis',
                                                                'Ilmuwan' => 'Ilmuwan',
                                                                'Agamawan' => 'Agamawan',
                                                                'Lainnya' => 'Lainnya',
                                                            ])
                                                            // ->searchable()
                                                            ->required()
                                                            //->default('Lainnya')
                                                            ->live()
                                                            ->native(false),

                                                        TextInput::make('cita_cita_lainnya')
                                                            ->label('Cita-cita Lainnya')
                                                            ->required()
                                                            //->default('asfasdad')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('cita_cita') !== 'Lainnya'),
                                                    ]),

                                                Grid::make(2)
                                                    ->schema([
                                                        Select::make('hobi')
                                                            ->label('Hobi')
                                                            ->placeholder('Pilih Hobi')
                                                            ->options([
                                                                'Olahraga' => 'Olahraga',
                                                                'Kesenian' => 'Kesenian',
                                                                'Membaca' => 'Membaca',
                                                                'Menulis' => 'Menulis',
                                                                'Jalan-jalan' => 'Jalan-jalan',
                                                                'Lainnya' => 'Lainnya',
                                                            ])
                                                            // ->searchable()
                                                            ->required()
                                                            //->default('Lainnya')
                                                            ->live()
                                                            ->native(false),

                                                        TextInput::make('hobi_lainnya')
                                                            ->label('Hobi Lainnya')
                                                            ->required()
                                                            //->default('asfasdad')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('hobi') !== 'Lainnya'),

                                                    ]),


                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                                Grid::make(2)
                                                    ->schema([
                                                        Select::make('keb_khus')
                                                            ->label('Kebutuhan Khusus')
                                                            ->placeholder('Pilih Kebutuhan Khusus')
                                                            ->options([
                                                                'Tidak Ada' => 'Tidak Ada',
                                                                'Lamban belajar' => 'Lamban belajar',
                                                                'Kesulitan belajar spesifik' => 'Kesulitan belajar spesifik',
                                                                'Gangguan komunikasi' => 'Gangguan komunikasi',
                                                                'Berbakat/memiliki kemampuan dan kecerdasan luar biasa' => 'Berbakat/memiliki kemampuan dan kecerdasan luar biasa',
                                                                'Lainnya' => 'Lainnya',
                                                            ])
                                                            // ->searchable()
                                                            ->required()
                                                            //->default('Lainnya')
                                                            ->live()
                                                            ->native(false),

                                                        TextInput::make('keb_khus_lainnya')
                                                            ->label('Kebutuhan Khusus Lainnya')
                                                            ->required()
                                                            //->default('asfasdad')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('keb_khus') !== 'Lainnya'),
                                                    ]),

                                                Grid::make(2)
                                                    ->schema([
                                                        Select::make('keb_dis')
                                                            ->label('Kebutuhan Disabilitas')
                                                            ->placeholder('Pilih Kebutuhan Disabilitas')
                                                            ->options([
                                                                'Tidak Ada' => 'Tidak Ada',
                                                                'Tuna Netra' => 'Tuna Netra',
                                                                'Tuna Rungu' => 'Tuna Rungu',
                                                                'Tuna Daksa' => 'Tuna Daksa',
                                                                'Tuna Grahita' => 'Tuna Grahita',
                                                                'Tuna Laras' => 'Tuna Laras',
                                                                'Tuna Wicara' => 'Tuna Wicara',
                                                                'Lainnya' => 'Lainnya',
                                                            ])
                                                            // ->searchable()
                                                            ->required()
                                                            //->default('Lainnya')
                                                            ->live()
                                                            ->native(false),

                                                        TextInput::make('keb_dis_lainnya')
                                                            ->label('Kebutuhan Disabilitas Lainnya')
                                                            ->required()
                                                            //->default('asfasdad')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('keb_dis') !== 'Lainnya'),
                                                    ]),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                                Grid::make(1)
                                                    ->schema([

                                                        Radio::make('tdk_hp')
                                                            ->label('Memiliki nomor handphone?')
                                                            ->live()
                                                            ->required()
                                                            //->default('Ya')
                                                            ->options([
                                                                'Ya' => 'Ya',
                                                                'Tidak' => 'Tidak',
                                                            ]),

                                                        TextInput::make('nomor_handphone')
                                                            ->label('No. Handphone')
                                                            ->helperText('Contoh: 82187782223')
                                                            // ->mask('82187782223')
                                                            ->prefix('62')
                                                            ->tel()
                                                            //->default('82187782223')
                                                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                                            ->required()
                                                            ->hidden(fn (Get $get) =>
                                                            $get('tdk_hp') !== 'Ya'),

                                                        TextInput::make('email')
                                                            ->label('Email')
                                                            //->default('mail@mail.com')
                                                            ->email(),
                                                    ]),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                                Grid::make(2)
                                                    ->schema([
                                                        Select::make('bya_sklh')
                                                            ->label('Yang membiayai sekolah')
                                                            ->placeholder('Pilih Yang membiayai sekolah')
                                                            ->options([
                                                                'Orang Tua' => 'Orang Tua',
                                                                'Wali/Orang Tua Asuh' => 'Wali/Orang Tua Asuh',
                                                                'Tanggungan Sendiri' => 'Tanggungan Sendiri',
                                                                'Lainnya' => 'Lainnya',
                                                            ])
                                                            // ->searchable()
                                                            ->required()
                                                            //->default('Lainnya')
                                                            ->live()
                                                            ->native(false),

                                                        TextInput::make('bya_sklh_lainnya')
                                                            ->label('Yang membiayai sekolah lainnya')
                                                            ->required()
                                                            //->default('asfasdad')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('bya_sklh') !== 'Lainnya'),
                                                    ]),

                                                Grid::make(2)
                                                    ->schema([

                                                        Radio::make('belum_nisn')
                                                            ->label('Apakah memiliki NISN?')
                                                            ->helperText(new HtmlString('<strong>NISN</strong> adalah Nomor Induk Siswa Nasional'))
                                                            ->live()
                                                            ->required()
                                                            //->default('Ya')
                                                            ->options([
                                                                'Ya' => 'Ya',
                                                                'Tidak' => 'Tidak',
                                                            ]),

                                                        TextInput::make('nisn')
                                                            ->label('Nomor NISN')
                                                            ->required()
                                                            //->default('2421324')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('belum_nisn') !== 'Ya'),
                                                    ]),

                                                Grid::make(2)
                                                    ->schema([

                                                        Radio::make('nomor_kip_memiliki')
                                                            ->label('Apakah memiliki KIP?')
                                                            ->helperText(new HtmlString('<strong>KIP</strong> adalah Kartu Indonesia Pintar'))
                                                            ->live()
                                                            ->required()
                                                            //->default('Ya')
                                                            ->options([
                                                                'Ya' => 'Ya',
                                                                'Tidak' => 'Tidak',
                                                            ]),

                                                        TextInput::make('nomor_kip')
                                                            ->label('Nomor KIP')
                                                            ->required()
                                                            //->default('32524324')
                                                            ->hidden(fn (Get $get) =>
                                                            $get('nomor_kip_memiliki') !== 'Ya'),
                                                    ]),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                                Grid::make(2)
                                                    ->schema([
                                                        TextInput::make('kartu_keluarga')
                                                            ->label('Nomor KK')
                                                            ->length(16)
                                                            ->required()
                                                            ->disabled()
                                                            ->dehydrated(),

                                                        TextInput::make('nama_kpl_kel')
                                                            ->label('Nama Kepala Keluarga')
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->required()
                                                            ->dehydrated(),
                                                    ]),

                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b"></div>')),

                                                TextInput::make('aktivitaspend')
                                                    ->label('Aktivitas Pendidikan yang Diikuti')
                                                    ->placeholder('Pilih Aktivitas Pendidikan yang Diikuti')
                                                    ->default('PKPPS')
                                                    ->hidden()
                                                    ->dehydrated(),

                                                // ALAMAT SANTRI
                                                Placeholder::make('')
                                                    ->content(new HtmlString('<div class="border-b">
                                                    <p class="text-lg strong"><strong>TEMPAT TINGGAL DOMISILI</strong></p>
                                                    <p class="text-lg strong"><strong>SANTRI</strong></p>
                                                </div>')),

                                                Radio::make('al_s_status_mukim')
                                                    ->label('Apakah mukim di Pondok?')
                                                    ->helperText(new HtmlString('Pilih <strong>Tidak Mukim</strong> khusus bagi pendaftar <strong>Tarbiyatul Aulaad</strong> dan <strong>Pra Tahfidz kelas 1-4</strong>'))
                                                    ->live()
                                                    ->required()
                                                    //->default('Tidak Mukim')
                                                    ->options([
                                                        'Mukim' => 'Mukim',
                                                        'Tidak Mukim' => 'Tidak Mukim',
                                                    ])
                                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                                        if ($get('al_s_status_mukim') === 'Mukim') {

                                                            $set('al_s_stts_tptgl', 'Tinggal di Asrama Pesantren');
                                                        } elseif ($get('al_s_status_mukim') === 'Tidak Mukim') {

                                                            $set('al_s_stts_tptgl', null);
                                                        }
                                                    }),

                                                Select::make('al_s_stts_tptgl')
                                                    ->label('Status tempat tinggal')
                                                    ->placeholder('Status tempat tinggal')
                                                    ->options([
                                                        'Tinggal dengan Ayah Kandung' => 'Tinggal dengan Ayah Kandung',
                                                        'Tinggal dengan Ibu Kandung' => 'Tinggal dengan Ibu Kandung',
                                                        'Tinggal dengan Wali' => 'Tinggal dengan Wali',
                                                        'Ikut Saudara/Kerabat' => 'Ikut Saudara/Kerabat',
                                                        'Kontrak/Kost' => 'Kontrak/Kost',
                                                        'Tinggal di Asrama Bukan Milik Pesantren' => 'Tinggal di Asrama Bukan Milik Pesantren',
                                                        'Panti Asuhan' => 'Panti Asuhan',
                                                        'Rumah Singgah' => 'Rumah Singgah',
                                                        'Lainnya' => 'Lainnya',
                                                    ])
                                                    // ->searchable()
                                                    ->required()
                                                    //->default('Kontrak/Kost')
                                                    ->live()
                                                    ->native(false)
                                                    ->dehydrated()
                                                    ->hidden(fn (Get $get) =>
                                                    $get('al_s_status_mukim') === 'Mukim'),

                                                Grid::make(2)
                                                    ->schema([

                                                        Select::make('al_s_provinsi_id')
                                                            ->label('Provinsi')
                                                            ->placeholder('Pilih Provinsi')
                                                            ->options(Provinsi::all()->pluck('provinsi', 'id'))
                                                            ->searchable()
                                                            //->default('35')
                                                            ->required()
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(
                                                                fn (Get $get) =>
                                                                $get('al_s_status_mukim') !== 'Tidak Mukim' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ayah Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ibu Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Wali' ||
                                                                    $get('al_s_stts_tptgl') === null
                                                            )
                                                            ->afterStateUpdated(function (Set $set) {
                                                                $set('al_s_kabupaten_id', null);
                                                                $set('al_s_kecamatan_id', null);
                                                                $set('al_s_kelurahan_id', null);
                                                                $set('al_s_kodepos', null);
                                                            }),

                                                        Select::make('al_s_kabupaten_id')
                                                            ->label('Kabupaten')
                                                            ->placeholder('Pilih Kabupaten')
                                                            ->options(fn (Get $get): Collection => Kabupaten::query()
                                                                ->where('provinsi_id', $get('al_s_provinsi_id'))
                                                                ->pluck('kabupaten', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            //->default('232')
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(
                                                                fn (Get $get) =>
                                                                $get('al_s_status_mukim') !== 'Tidak Mukim' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ayah Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ibu Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Wali' ||
                                                                    $get('al_s_stts_tptgl') === null
                                                            ),

                                                        Select::make('al_s_kecamatan_id')
                                                            ->label('Kecamatan')
                                                            ->placeholder('Pilih Kecamatan')
                                                            ->options(fn (Get $get): Collection => Kecamatan::query()
                                                                ->where('kabupaten_id', $get('al_s_kabupaten_id'))
                                                                ->pluck('kecamatan', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            //->default('3617')
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(
                                                                fn (Get $get) =>
                                                                $get('al_s_status_mukim') !== 'Tidak Mukim' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ayah Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ibu Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Wali' ||
                                                                    $get('al_s_stts_tptgl') === null
                                                            ),

                                                        Select::make('al_s_kelurahan_id')
                                                            ->label('Kelurahan')
                                                            ->placeholder('Pilih Kelurahan')
                                                            ->options(fn (Get $get): Collection => Kelurahan::query()
                                                                ->where('kecamatan_id', $get('al_s_kecamatan_id'))
                                                                ->pluck('kelurahan', 'id'))
                                                            ->searchable()
                                                            ->required()
                                                            //->default('45322')
                                                            ->live()
                                                            ->native(false)
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            ->hidden(
                                                                fn (Get $get) =>
                                                                $get('al_s_status_mukim') !== 'Tidak Mukim' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ayah Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ibu Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Wali' ||
                                                                    $get('al_s_stts_tptgl') === null
                                                            )
                                                            ->afterStateUpdated(function (Get $get, ?string $state, Set $set, ?string $old) {

                                                                $kodepos = Kodepos::where('kelurahan_id', $state)->get('kodepos');

                                                                $state = $kodepos;

                                                                foreach ($state as $state) {
                                                                    $set('al_s_kodepos', Str::substr($state, 12, 5));
                                                                }
                                                            }),


                                                        TextInput::make('al_s_rt')
                                                            ->label('RT')
                                                            ->required()
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            //->default('2')
                                                            ->hidden(
                                                                fn (Get $get) =>
                                                                $get('al_s_status_mukim') !== 'Tidak Mukim' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ayah Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ibu Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Wali' ||
                                                                    $get('al_s_stts_tptgl') === null
                                                            ),

                                                        TextInput::make('al_s_rw')
                                                            ->label('RW')
                                                            ->required()
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            //->default('2')
                                                            ->hidden(
                                                                fn (Get $get) =>
                                                                $get('al_s_status_mukim') !== 'Tidak Mukim' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ayah Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ibu Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Wali' ||
                                                                    $get('al_s_stts_tptgl') === null
                                                            ),

                                                        Textarea::make('al_s_alamat')
                                                            ->label('Alamat')
                                                            ->required()
                                                            ->columnSpanFull()
                                                            ->hint('Isi sesuai dengan KK')
                                                            ->hintColor('danger')
                                                            //->default('sdfsdasdada')
                                                            ->hidden(
                                                                fn (Get $get) =>
                                                                $get('al_s_status_mukim') !== 'Tidak Mukim' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ayah Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ibu Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Wali' ||
                                                                    $get('al_s_stts_tptgl') === null
                                                            ),

                                                        TextInput::make('al_s_kodepos')
                                                            ->label('Kodepos')
                                                            ->disabled()
                                                            ->required()
                                                            ->dehydrated()
                                                            //->default('63264')
                                                            ->hidden(
                                                                fn (Get $get) =>
                                                                $get('al_s_status_mukim') !== 'Tidak Mukim' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ayah Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Ibu Kandung' ||
                                                                    $get('al_s_stts_tptgl') === 'Tinggal dengan Wali' ||
                                                                    $get('al_s_stts_tptgl') === null
                                                            ),


                                                        Grid::make(3)
                                                            ->schema([
                                                                Select::make('al_s_jarak')
                                                                    ->label('Jarak tempat tinggal ke Pondok Pesantren')
                                                                    ->options([
                                                                        'Kurang dari 5 km' => 'Kurang dari 5 km',
                                                                        'Antara 5 - 10 Km' => 'Antara 5 - 10 Km',
                                                                        'Antara 11 - 20 Km' => 'Antara 11 - 20 Km',
                                                                        'Antara 21 - 30 Km' => 'Antara 21 - 30 Km',
                                                                        'Lebih dari 30 Km' => 'Lebih dari 30 Km',
                                                                    ])
                                                                    // ->searchable()
                                                                    ->required()
                                                                    //->default('Kurang dari 5 km')
                                                                    ->live()
                                                                    ->native(false)
                                                                    ->hidden(
                                                                        fn (Get $get) =>
                                                                        $get('al_s_status_mukim') !== 'Tidak Mukim' ||
                                                                            $get('al_s_stts_tptgl') === null
                                                                    ),

                                                                Select::make('al_s_transportasi')
                                                                    ->label('Transportasi ke Pondok Pesantren')
                                                                    ->options([
                                                                        'Jalan kaki' => 'Jalan kaki',
                                                                        'Sepeda' => 'Sepeda',
                                                                        'Sepeda Motor' => 'Sepeda Motor',
                                                                        'Mobil Pribadi' => 'Mobil Pribadi',
                                                                        'Antar Jemput Sekolah' => 'Antar Jemput Sekolah',
                                                                        'Angkutan Umum' => 'Angkutan Umum',
                                                                        'Perahu/Sampan' => 'Perahu/Sampan',
                                                                        'Lainnya' => 'Lainnya',
                                                                        'Kendaraan Pribadi' => 'Kendaraan Pribadi',
                                                                        'Kereta Api' => 'Kereta Api',
                                                                        'Ojek' => 'Ojek',
                                                                        'Andong/Bendi/Sado/Dokar/Delman/Becak' => 'Andong/Bendi/Sado/Dokar/Delman/Becak',
                                                                    ])
                                                                    // ->searchable()
                                                                    ->required()
                                                                    //->default('Ojek')
                                                                    ->live()
                                                                    ->native(false)
                                                                    ->hidden(
                                                                        fn (Get $get) =>
                                                                        $get('al_s_status_mukim') !== 'Tidak Mukim' ||
                                                                            $get('al_s_stts_tptgl') === null
                                                                    ),

                                                                Select::make('al_s_waktu_tempuh')
                                                                    ->label('Waktu tempuh ke Pondok Pesantren')
                                                                    ->options([
                                                                        '1 - 10 menit' => '1 - 10 menit',
                                                                        '10 - 19 menit' => '10 - 19 menit',
                                                                        '20 - 29 menit' => '20 - 29 menit',
                                                                        '30 - 39 menit' => '30 - 39 menit',
                                                                        '1 - 2 jam' => '1 - 2 jam',
                                                                        '> 2 jam' => '> 2 jam',
                                                                    ])
                                                                    // ->searchable()
                                                                    ->required()
                                                                    //->default('10 - 19 menit')
                                                                    ->live()
                                                                    ->native(false)
                                                                    ->hidden(
                                                                        fn (Get $get) =>
                                                                        $get('al_s_status_mukim') !== 'Tidak Mukim' ||
                                                                            $get('al_s_stts_tptgl') === null
                                                                    ),

                                                                TextInput::make('al_s_koordinat')
                                                                    ->label('Titik koordinat tempat tinggal')
                                                                    //->default('sfasdadasdads')
                                                                    ->hidden(
                                                                        fn (Get $get) =>
                                                                        $get('al_s_status_mukim') !== 'Tidak Mukim' ||
                                                                            $get('al_s_stts_tptgl') === null
                                                                    )->columnSpanFull(),
                                                            ]),
                                                    ]),

                                                // end of step 2
                                            ])
                                    ]),
                                // end of Santri Tab





                            ])->columnSpanFull()

                    ])
            ]);
    }
}
