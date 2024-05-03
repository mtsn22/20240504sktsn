<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AdminKelasSantriResource\Pages;
use App\Filament\Admin\Resources\AdminKelasSantriResource\RelationManagers;
use App\Models\AdminKelasSantri;
use App\Models\KelasSantri;
use App\Models\Santri;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class AdminKelasSantriResource extends Resource
{
    public static function canViewAny(): bool
    {
        return auth()->user()->id == 1;
    }

    protected static ?string $navigationGroup = 'Kesantrian';

    protected static ?int $navigationSort = 04010;

    protected static ?string $modelLabel = 'Kelas Santri';

    protected static ?string $navigationLabel = 'Kelas Santri';

    protected static ?string $pluralModelLabel = 'Kelas Santri';

    protected static ?string $model = KelasSantri::class;

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
            ->columns([

                 TextColumn::make('walisantri.ws_emis4')
                    ->label('WS Lengkap')
                    ->searchable(isIndividual: true)
                    ->sortable(),

                    TextColumn::make('santri.s_emis4')
                    ->label('S Lengkap')
                    ->searchable(isIndividual: true)
                    ->sortable(),

                    TextColumn::make('walisantri.tanggal_datang')
                    ->label('Kedatangan')
                    ->date()
                    ->searchable(isIndividual: true)
                    ->sortable(),

                TextColumn::make('walisantri.user.id')
                    ->label('User ID')
                    ->searchable(isIndividual: true)
                    ->sortable(),

                    TextColumn::make('walisantri.user.username')
                    ->label('Username')
                    ->searchable(isIndividual: true)
                    ->sortable(),

                TextColumn::make('kartu_keluarga')
                    ->label('KK')
                    ->searchable(isIndividual: true)
                    ->sortable(),

                    TextColumn::make('walisantri_id')
                    ->label('Walisantri ID')
                    ->searchable(isIndividual: true)
                    ->sortable(),

                TextColumn::make('walisantri.ak_nama_lengkap')
                    ->label('Walisantri')
                    ->searchable(isIndividual: true)
                    ->sortable(),


                TextColumn::make('santri_id')
                    ->label('Santri ID')
                    ->searchable(isIndividual: true)
                    ->sortable(),

                TextColumn::make('santri.nama_lengkap')
                    ->label('Santri')
                    ->searchable(isIndividual: true)
                    ->sortable(),



            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([

                Tables\Actions\BulkAction::make('updatewsid')
                    ->label(__('Update Walisantri ID'))
                    ->color('success')
                    // ->requiresConfirmation()
                    // ->modalIcon('heroicon-o-check-circle')
                    // ->modalIconColor('success')
                    // ->modalHeading('Simpan data santri tinggal kelas?')
                    // ->modalDescription('Setelah klik tombol "Simpan", maka status akan berubah')
                    // ->modalSubmitActionLabel('Simpan')
                    ->action(fn (Collection $records, array $data) => $records->each(
                        function ($record) {

                            $wsid = Santri::where('id', $record->santri_id)->first();

                            if ($wsid === null) {
                                return;
                            } elseif ($wsid !== null) {
                                // dd($record->santri_id, $wsid->walisantri_id);
                                // $walisantriid = KelasSantri::where('santri_id', $record->santri_id)->get();
                                // $walisantriid->walisantri_id = $wsid->walisantri_id;
                                // $walisantriid->save();

                                $data['walisantri_id'] = $wsid->walisantri_id;
                                $record->update($data);

                                return $record;
                            }
                            // Notification::make()
                            //     // ->success()
                            //     ->title('Walisantri ID berhasil diupdate')
                            //     ->icon('heroicon-o-exclamation-triangle')
                            //     ->iconColor('danger')
                            //     ->color('warning')
                            //     ->send();
                        }
                    ))
                    ->deselectRecordsAfterCompletion(),

                Tables\Actions\BulkAction::make('updatekk')
                    ->label(__('Update KK'))
                    ->color('success')
                    // ->requiresConfirmation()
                    // ->modalIcon('heroicon-o-check-circle')
                    // ->modalIconColor('success')
                    // ->modalHeading('Simpan data santri tinggal kelas?')
                    // ->modalDescription('Setelah klik tombol "Simpan", maka status akan berubah')
                    // ->modalSubmitActionLabel('Simpan')
                    ->action(fn (Collection $records, array $data) => $records->each(
                        function ($record) {

                            $wsid = Santri::where('id', $record->santri_id)->first();

                            if ($wsid === null) {
                                return;
                            } elseif ($wsid !== null) {
                                // dd($record->santri_id, $wsid->walisantri_id);
                                // $walisantriid = KelasSantri::where('santri_id', $record->santri_id)->get();
                                // $walisantriid->walisantri_id = $wsid->walisantri_id;
                                // $walisantriid->save();

                                $data['kartu_keluarga'] = $wsid->kartu_keluarga;
                                $record->update($data);

                                return $record;
                            }
                            // Notification::make()
                            //     // ->success()
                            //     ->title('Walisantri ID berhasil diupdate')
                            //     ->icon('heroicon-o-exclamation-triangle')
                            //     ->iconColor('danger')
                            //     ->color('warning')
                            //     ->send();
                        }
                    ))
                    ->deselectRecordsAfterCompletion(),

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
            'index' => Pages\ListAdminKelasSantris::route('/'),
            'create' => Pages\CreateAdminKelasSantri::route('/create'),
            'view' => Pages\ViewAdminKelasSantri::route('/{record}'),
            'edit' => Pages\EditAdminKelasSantri::route('/{record}/edit'),
        ];
    }
}
