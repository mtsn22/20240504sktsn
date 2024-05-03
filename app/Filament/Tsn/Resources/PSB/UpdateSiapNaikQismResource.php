<?php

namespace App\Filament\Tsn\Resources\PSB;

use App\Filament\Tsn\Resources\PSB\UpdateSiapNaikQismResource\Pages;
use App\Models\KelasSantri;
use App\Models\Santri;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Form;

use function Laravel\Prompts\text;

class UpdateSiapNaikQismResource extends Resource
{

    public static function canViewAny(): bool
    {
        return auth()->user()->id == 1;
    }

    protected static ?string $navigationGroup = 'PSB';

    protected static ?int $navigationSort = 01030;

    protected static ?string $modelLabel = 'Update Status untuk Naik Qism';

    protected static ?string $navigationLabel = 'Update Status untuk Naik Qism';

    protected static ?string $pluralModelLabel = 'Update Status untuk Naik Qism';

    protected static ?string $model = Santri::class;

    protected static ?string $navigationIcon = 'heroicon-s-user-plus';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama_lengkap'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_lengkap')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jeniskelamin')
                    ->label('Jenis Kelamin')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kelassantri.qism_detail.abbr_qism_detail')
                    ->label('Qism')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                TextColumn::make('kelassantri.kelas.kelas')
                    ->label('Kelas')
                    ->searchable(isIndividual: true)
                    ->sortable(),

                TextColumn::make('naikqism')
                    ->label('Naik Qism')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('naikqism')
                    ->label(__('Siap Naik Qism'))
                    ->action(fn (Collection $records, array $data) => $records->each(function ($record) {

                        $kelassantri = KelasSantri::where('is_active', '1')->where('santri_id', $record->id)->first();
                        // dd($kelassantri->qism_id, $kelassantri->qism_detail_id);



                        switch (true) {
                            case ($kelassantri->qism_id === '1' && $kelassantri->qism_detail_id === '1'):

                                $data['naikqism'] = 'naik';
                                $data['qism_id'] = '2';
                                $data['qism'] = 'Pra Tahfidz';
                                $data['qism_detail_id'] = '3';
                                $data['qism_detail'] = 'Pra Tahfidz Putra';
                                $record->update($data);

                                return $record;

                                break;

                            case ($kelassantri->qism_id === '1' && $kelassantri->qism_detail_id === '2'):
                                $data['naikqism'] = 'naik';
                                $data['qism_id'] = '2';
                                $data['qism'] = 'Pra Tahfidz';
                                $data['qism_detail_id'] = '4';
                                $data['qism_detail'] = 'Pra Tahfidz Putri';
                                $record->update($data);

                                return $record;

                                break;

                            case ($kelassantri->qism_id === '2' && $kelassantri->qism_detail_id === '3'):
                                // dd('a');
                                $data['naikqism'] = 'naik';
                                $data['qism_id'] = '3';
                                $data['qism'] = 'Tahfidzul Quran';
                                $data['qism_detail_id'] = '5';
                                $data['qism_detail'] = 'Tahfidzul Quran Putra';
                                $record->update($data);

                                return $record;

                                break;

                            case ($kelassantri->qism_id === '2' && $kelassantri->qism_detail_id === '4'):
                                $data['naikqism'] = 'naik';
                                $data['qism_id'] = '3';
                                $data['qism'] = 'Tahfidzul Quran';
                                $data['qism_detail_id'] = '6';
                                $data['qism_detail'] = 'Tahfidzul Quran Putri';
                                $record->update($data);

                                return $record;

                                break;

                            case ($kelassantri->qism_id === '3' && $kelassantri->qism_detail_id === '5'):
                                $data['naikqism'] = 'naik';
                                $data['qism_id'] = '4';
                                $data['qism'] = 'Idad Lughoh';
                                $data['qism_detail_id'] = '7';
                                $data['qism_detail'] = 'Idad Lughoh';
                                $record->update($data);

                                return $record;

                                break;

                            case ($kelassantri->qism_id === '3' && $kelassantri->qism_detail_id === '6'):
                                $data['naikqism'] = 'naik';
                                $data['qism_id'] = '5';
                                $data['qism'] = 'Mutawasithoh';
                                $data['qism_detail_id'] = '8';
                                $data['qism_detail'] = 'Mutawasithoh';
                                $record->update($data);

                                return $record;

                                break;

                            case ($kelassantri->qism_id === '5' && $kelassantri->qism_detail_id === '8'):
                                $data['naikqism'] = 'naik';
                                $data['qism_id'] = '6';
                                $data['qism'] = 'Tarbiyatunnisaa';
                                $data['qism_detail_id'] = '9';
                                $data['qism_detail'] = 'Tarbiyatunnisaa';
                                $record->update($data);

                                return $record;

                                break;

                            default:
                                // dd('a');

                                Notification::make()
                                    // ->success()
                                    ->title('Belum saatnya naik qism')
                                    ->persistent()
                                    ->color('Warning')
                                    ->send();
                        }
                    }))->deselectRecordsAfterCompletion()
            ])
            ->groups([
                'kelassantri.qism_detail.abbr_qism_detail',
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
            'index' => Pages\ListUpdateSiapNaikQisms::route('/'),
            'create' => Pages\CreateUpdateSiapNaikQism::route('/create'),
            'view' => Pages\ViewUpdateSiapNaikQism::route('/{record}'),
            'edit' => Pages\EditUpdateSiapNaikQism::route('/{record}/edit'),
        ];
    }

    // public static function getEloquentQuery(): Builder
    // {

    //     // $mudirqism = Auth::user()->mudirqism;
    //     // dd($mudirqism);

    //     if (Auth::user()->id === '3'0) {
    //         return parent::getEloquentQuery()->whereIn('ps_qism', [5,6]);
    //     }else{
    //         return parent::getEloquentQuery()->where('qism_calon', Auth::user()->mudirqism);
    //     }

    // }

    public static function getEloquentQuery(): Builder
    {

        return parent::getEloquentQuery()->whereHas('kelasSantris', function ($query) {
            $query->whereIn('qism_id', Auth::user()->mudirqism)->where('is_active', '1');
        });
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()->whereHas('walisantri.user', function ($query) {
    //         $query->where('id', Auth::user()->id);
    //     });
    // }
}
