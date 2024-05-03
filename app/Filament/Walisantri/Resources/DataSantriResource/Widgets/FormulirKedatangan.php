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
use App\Models\PesanDaftar;
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

class FormulirKedatangan extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {

        $tahunberjalanaktif = TahunBerjalan::where('is_active', 1)->first();
        $ts = TahunBerjalan::where('tb', $tahunberjalanaktif->ts)->first();

        return $table
            ->heading('Formulir Data Tamu Kedatangan')
            ->paginated(false)
            ->query(

                Walisantri::where('ak_no_kk', Auth::user()->username)
            )
            ->columns([
                Stack::make([
                    TextColumn::make('ak_kep_kel_kk')
                        ->label('Nama')
                        ->size(TextColumn\TextColumnSize::Large)
                        ->weight(FontWeight::Bold),

                    TextColumn::make('putra')
                        ->label('Jumlah Tamu Putra')
                        ->description(fn ($record): string => "Jumlah Tamu Putra:", position: 'above')
                        ->default(new HtmlString('')),

                    TextColumn::make('putri')
                        ->label('Jumlah Tamu Putri')
                        ->description(fn ($record): string => "Jumlah Tamu Putri:", position: 'above')
                        ->default(new HtmlString('')),

                    TextColumn::make('tanggal_datang')
                        ->label('Tanggal Datang')
                        ->date()
                        ->description(fn ($record): string => "Tanggal Datang:", position: 'above')
                        ->default(new HtmlString('')),

                    TextColumn::make('waktu_datang')
                        ->label('Waktu Datang')
                        ->description(fn ($record): string => "Waktu Datang:", position: 'above')
                        ->default(new HtmlString('')),

                    TextColumn::make('tanggal_kembali')
                        ->label('Tanggal Kembali')
                        ->date()
                        ->description(fn ($record): string => "Tanggal Kembali:", position: 'above')
                        ->default(new HtmlString('')),

                    TextColumn::make('waktu_kembali')
                        ->label('Waktu Kembali')
                        ->description(fn ($record): string => "Waktu Kembali:", position: 'above')
                        ->default(new HtmlString('')),

                    TextColumn::make('jumlah_hari')
                        ->label('Jumlah Hari')
                        ->description(fn ($record): string => "Jumlah Hari:", position: 'above')
                        ->default(new HtmlString('')),

                        TextColumn::make('menginap')
                        ->label('menginap')
                        ->description(fn ($record): string => "Status Menginap:", position: 'above')
                        ->default(new HtmlString('')),

                    TextColumn::make('informasi_lain')
                        ->label('Informasi Lain')
                        ->description(fn ($record): string => "Informasi Lain:", position: 'above')
                        ->default(new HtmlString('')),

                    TextColumn::make('a')
                        ->default(new HtmlString('</br>Silakan mulai mengisi formulir dengan klik tombol di bawah ini')),

                ])
            ])
            ->contentGrid([
                'md' => 1,
                'xl' => 1,
            ])
            ->actions([

                Tables\Actions\EditAction::make()
                    ->label('Formulir Kedatangan')
                    ->modalCloseButton(false)
                    ->modalHeading(' ')
                    ->modalWidth('full')
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
                                        return (new HtmlString('<div><p class="text-3xl"><strong>' . $record->ak_kep_kel_kk . '</strong></p></div>'));
                                    }),

                                TextInput::make('putra')
                                    ->label('Keluarga Putra Datang')
                                    ->numeric()
                                    ->hint('Termasuk Santri')
                                    ->hintColor('danger')
                                    ->required(),

                                TextInput::make('putri')
                                    ->label('Keluarga Putri Datang')
                                    ->numeric()
                                    ->hint('Termasuk Santriwati')
                                    ->hintColor('danger')
                                    ->required(),

                                DatePicker::make('tanggal_datang')
                                    ->label('Tanggal Datang')
                                    // ->helperText('Format: bulan/hari/tahun')
                                    ->required()
                                    ->format('Y-m-d')
                                    ->displayFormat('d/m/Y')
                                    ->closeOnDateSelection(),

                                Select::make('waktu_datang')
                                    ->label('Waktu Datang')
                                    ->placeholder('Pilih Waktu Kedatangan')
                                    ->options([
                                        'Pagi' => 'Pagi',
                                        'Siang' => 'Siang',
                                        'Sore' => 'Sore',
                                    ])
                                    ->required()
                                    ->native(false),

                                DatePicker::make('tanggal_kembali')
                                    ->label('Tanggal Kembali')
                                    // ->helperText('Format: bulan/hari/tahun')
                                    ->required()
                                    ->format('Y-m-d')
                                    ->displayFormat('d/m/Y')
                                    ->closeOnDateSelection(),

                                Select::make('waktu_kembali')
                                    ->label('Waktu Kembali')
                                    ->placeholder('Pilih Waktu Kembali')
                                    ->options([
                                        'Pagi' => 'Pagi',
                                        'Siang' => 'Siang',
                                        'Sore' => 'Sore',
                                    ])
                                    ->required()
                                    ->native(false),

                                Textarea::make('informasi_lain')
                                    ->label('Informasi Lain'),



                            ])->columnSpanFull()

                    ])
                    ->after(function ($record) {

                        $tahunberjalanaktif = TahunBerjalan::where('is_active', 1)->first();

                        $ts = TahunBerjalan::where('tb', $tahunberjalanaktif->ts)->first();

                        $cekpesandaftar = PesanDaftar::where('tahun_berjalan_id', $tahunberjalanaktif->id)
                            ->where('walisantri_id', $record->id)->count();

                        $datang = Carbon::parse($record->tanggal_datang);
                        $kembali = Carbon::parse($record->tanggal_kembali);
                        $jumlahhari = $kembali->diffInDays($datang);

                        if ($cekpesandaftar === 0) {

                            if ($jumlahhari === 0) {

                                $data['jumlah_hari'] = $kembali->diffInDays($datang);
                                $data['menginap'] = 'Tidak Menginap';
                                $record->update($data);

                                $pesandaftar = new PesanDaftar();

                                $pesandaftar->tahun_berjalan_id = $tahunberjalanaktif->id;
                                $pesandaftar->walisantri_id = $record->id;
                                $pesandaftar->putra = $record->putra;
                                $pesandaftar->putri = $record->putri;
                                $pesandaftar->tanggal_datang = $record->tanggal_datang;
                                $pesandaftar->waktu_datang = $record->waktu_datang;
                                $pesandaftar->tanggal_kembali = $record->tanggal_kembali;
                                $pesandaftar->waktu_kembali = $record->waktu_kembali;
                                $pesandaftar->jumlah_hari = $kembali->diffInDays($datang);
                                $pesandaftar->menginap = 'Tidak Menginap';
                                $pesandaftar->informasi_lain = $record->informasi_lain;

                                $pesandaftar->save();

                                Notification::make()
                                    ->success()
                                    ->title('Alhamdulillah data telah tersimpan')
                                    ->persistent()
                                    ->color('success')
                                    ->send();
                            } elseif ($jumlahhari !== 0) {

                                $data['jumlah_hari'] = $kembali->diffInDays($datang);
                                $data['menginap'] = 'Menginap';
                                $record->update($data);

                                $pesandaftar = new PesanDaftar();

                                $pesandaftar->tahun_berjalan_id = $tahunberjalanaktif->id;
                                $pesandaftar->walisantri_id = $record->id;
                                $pesandaftar->putra = $record->putra;
                                $pesandaftar->putri = $record->putri;
                                $pesandaftar->tanggal_datang = $record->tanggal_datang;
                                $pesandaftar->waktu_datang = $record->waktu_datang;
                                $pesandaftar->tanggal_kembali = $record->tanggal_kembali;
                                $pesandaftar->waktu_kembali = $record->waktu_kembali;
                                $pesandaftar->jumlah_hari = $kembali->diffInDays($datang);
                                $pesandaftar->menginap = 'Menginap';
                                $pesandaftar->informasi_lain = $record->informasi_lain;

                                $pesandaftar->save();

                                Notification::make()
                                    ->success()
                                    ->title('Alhamdulillah data telah tersimpan')
                                    ->persistent()
                                    ->color('success')
                                    ->send();
                            }
                        } elseif ($cekpesandaftar !== 0) {

                            if ($jumlahhari === 0) {

                                $data['putra'] = $record->putra;
                                $data['putri'] = $record->putri;
                                $data['tanggal_datang'] = $record->tanggal_datang;
                                $data['waktu_datang'] = $record->waktu_datang;
                                $data['tanggal_kembali'] = $record->tanggal_kembali;
                                $data['waktu_kembali'] = $record->waktu_kembali;
                                $data['jumlah_hari'] = $kembali->diffInDays($datang);
                                $data['menginap'] = 'Tidak Menginap';
                                $data['informasi_lain'] = $record->informasi_lain;
                                $record->update($data);


                                $pesandaftar = PesanDaftar::where('tahun_berjalan_id', $tahunberjalanaktif->id)
                                    ->where('walisantri_id', $record->id)->first();
                                $pesandaftar->putra = $record->putra;
                                $pesandaftar->putri = $record->putri;
                                $pesandaftar->tanggal_datang = $record->tanggal_datang;
                                $pesandaftar->waktu_datang = $record->waktu_datang;
                                $pesandaftar->tanggal_kembali = $record->tanggal_kembali;
                                $pesandaftar->waktu_kembali = $record->waktu_kembali;
                                $pesandaftar->jumlah_hari = $kembali->diffInDays($datang);
                                $pesandaftar->menginap = 'Tidak Menginap';
                                $pesandaftar->informasi_lain = $record->informasi_lain;
                                $pesandaftar->save();

                                Notification::make()
                                    ->success()
                                    ->title('Alhamdulillah data telah tersimpan')
                                    ->persistent()
                                    ->color('success')
                                    ->send();
                            } elseif ($jumlahhari !== 0) {

                                $data['putra'] = $record->putra;
                                $data['putri'] = $record->putri;
                                $data['tanggal_datang'] = $record->tanggal_datang;
                                $data['waktu_datang'] = $record->waktu_datang;
                                $data['tanggal_kembali'] = $record->tanggal_kembali;
                                $data['waktu_kembali'] = $record->waktu_kembali;
                                $data['jumlah_hari'] = $kembali->diffInDays($datang);
                                $data['menginap'] = 'Menginap';
                                $data['informasi_lain'] = $record->informasi_lain;
                                $record->update($data);


                                $pesandaftar = PesanDaftar::where('tahun_berjalan_id', $tahunberjalanaktif->id)
                                    ->where('walisantri_id', $record->id)->first();
                                $pesandaftar->putra = $record->putra;
                                $pesandaftar->putri = $record->putri;
                                $pesandaftar->tanggal_datang = $record->tanggal_datang;
                                $pesandaftar->waktu_datang = $record->waktu_datang;
                                $pesandaftar->tanggal_kembali = $record->tanggal_kembali;
                                $pesandaftar->waktu_kembali = $record->waktu_kembali;
                                $pesandaftar->jumlah_hari = $kembali->diffInDays($datang);
                                $pesandaftar->menginap = 'Menginap';
                                $pesandaftar->informasi_lain = $record->informasi_lain;
                                $pesandaftar->save();

                                Notification::make()
                                    ->success()
                                    ->title('Alhamdulillah data telah tersimpan')
                                    ->persistent()
                                    ->color('success')
                                    ->send();
                            }
                        }
                    }),

            ]);
    }
}
