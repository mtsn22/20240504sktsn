<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TsnuniqueResource\Pages;
use App\Filament\Admin\Resources\TsnuniqueResource\RelationManagers;
use App\Models\Tsnunique;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;

class TsnuniqueResource extends Resource
{

    public static function canViewAny(): bool
    {
        return auth()->user()->id == 1;
    }

    protected static ?string $navigationGroup = 'User';

    protected static ?int $navigationSort = 99020;

    protected static ?string $modelLabel = 'tsnunique';

    protected static ?string $navigationLabel = 'tsnunique';

    protected static ?string $pluralModelLabel = 'tsnunique';

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
            ->defaultPaginationPageOption('20')
            ->recordUrl(null)
            ->columns([

                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(isIndividual: true, isGlobal: false),

                TextColumn::make('username')
                    ->label('KK')
                    ->searchable(isIndividual: true, isGlobal: false),

                TextInputColumn::make('tsnunique')
                    ->label('Unique'),

                TextColumn::make('email_verified_at')
                    ->label('User Pass')
                    ->default(function (Model $record) {

                        return (new HtmlString('<div>Bismillah, </br>
                            __________</br>
                            Username:</br></br>
                            ' . $record->username . '</br></br>
                            __________</br></br>
                            Password:</br></br>
                            ' . $record->tsnunique . '</div>'));
                    })
                    ->copyable()
                    ->copyableState(function (Model $record) {

                        return (new HtmlString(
                            'Bismillah,
                            __________

                            Username:

                            ' . $record->username . '

                            __________

                            Password:

                            ' . $record->tsnunique . ''
                        ));
                    })
                    ->copyMessage('Tersalin')
                    ->copyMessageDuration(1500),

                CheckboxColumn::make('is_request')
                    ->label('Req')
                    ->alignCenter(),

                TextColumn::make('panelrole')
                    ->label('Panel')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('channel')
                    ->label('Channel')
                    ->toggleable(isToggledHiddenByDefault: true),

                CheckboxColumn::make('is_active')
                    ->label('Status')
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('panelrole')
                    ->label('Panel')
                    ->multiple()
                    ->options([
                        'admin' => 'admin',
                        'pengajar' => 'pengajar',
                        'walisantri' => 'walisantri',
                        'psb' => 'psb',
                    ]),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([

                BulkAction::make('Ubah Unique')
                    ->label(__('Ubah Unique'))
                    ->color('info')
                    // ->requiresConfirmation()
                    // ->modalIcon('heroicon-o-check-circle')
                    // ->modalIconColor('success')
                    // ->modalHeading('Ubah Status menjadi "Diterima sebagai santri?"')
                    // ->modalDescription('Setelah klik tombol "Simpan", maka status akan berubah')
                    // ->modalSubmitActionLabel('Simpan')
                    ->action(fn (Collection $records, array $data) => $records->each(
                        function ($record) {

                            $password = $record->tsnunique;
                            $updatepassword = Hash::make($password);

                            User::where('username', $record->username)
                                ->update(['password' => $updatepassword]);

                            Notification::make()
                                // ->success()
                                ->title('Unique berhasil diubah')
                                ->icon('heroicon-o-exclamation-triangle')
                                ->iconColor('success')
                                // ->persistent()
                                ->color('success')
                                ->send();
                        }
                    ))
                    ->deselectRecordsAfterCompletion(),

                BulkAction::make('Set Aktif')
                    ->label(__('Set Aktif'))
                    ->color('success')
                    // ->requiresConfirmation()
                    // ->modalIcon('heroicon-o-check-circle')
                    // ->modalIconColor('success')
                    // ->modalHeading('Ubah Status menjadi "Diterima sebagai santri?"')
                    // ->modalDescription('Setelah klik tombol "Simpan", maka status akan berubah')
                    // ->modalSubmitActionLabel('Simpan')
                    ->action(fn (Collection $records, array $data) => $records->each(
                        function ($record) {

                            User::where('username', $record->username)
                                ->update(['is_active' => 1]);

                            Notification::make()
                                // ->success()
                                ->title('Status User berhasil diubah')
                                ->icon('heroicon-o-exclamation-triangle')
                                ->iconColor('success')
                                // ->persistent()
                                ->color('success')
                                ->send();
                        }
                    ))
                    ->deselectRecordsAfterCompletion(),

                BulkAction::make('Set Tidak Aktif')
                    ->label(__('Set Tidak Aktif'))
                    ->color('danger')
                    // ->requiresConfirmation()
                    // ->modalIcon('heroicon-o-check-circle')
                    // ->modalIconColor('success')
                    // ->modalHeading('Ubah Status menjadi "Diterima sebagai santri?"')
                    // ->modalDescription('Setelah klik tombol "Simpan", maka status akan berubah')
                    // ->modalSubmitActionLabel('Simpan')
                    ->action(fn (Collection $records, array $data) => $records->each(
                        function ($record) {

                            User::where('username', $record->username)
                                ->update(['is_active' => 0]);

                            Notification::make()
                                // ->success()
                                ->title('Status User berhasil diubah')
                                ->icon('heroicon-o-exclamation-triangle')
                                ->iconColor('success')
                                // ->persistent()
                                ->color('success')
                                ->send();
                        }
                    ))
                    ->deselectRecordsAfterCompletion(),

                BulkAction::make('Set Panel Admin')
                    ->label(__('Set Panel Admin'))
                    ->color('warning')
                    // ->requiresConfirmation()
                    // ->modalIcon('heroicon-o-check-circle')
                    // ->modalIconColor('success')
                    // ->modalHeading('Ubah Status menjadi "Diterima sebagai santri?"')
                    // ->modalDescription('Setelah klik tombol "Simpan", maka status akan berubah')
                    // ->modalSubmitActionLabel('Simpan')
                    ->action(fn (Collection $records, array $data) => $records->each(
                        function ($record) {

                            User::where('username', $record->username)
                                ->update(['panelrole' => 'admin']);

                            Notification::make()
                                // ->success()
                                ->title('Status User berhasil diubah')
                                ->icon('heroicon-o-exclamation-triangle')
                                ->iconColor('success')
                                // ->persistent()
                                ->color('success')
                                ->send();
                        }
                    ))
                    ->deselectRecordsAfterCompletion(),

                BulkAction::make('Set Panel TSN')
                    ->label(__('Set Panel TSN'))
                    ->color('warning')
                    // ->requiresConfirmation()
                    // ->modalIcon('heroicon-o-check-circle')
                    // ->modalIconColor('success')
                    // ->modalHeading('Ubah Status menjadi "Diterima sebagai santri?"')
                    // ->modalDescription('Setelah klik tombol "Simpan", maka status akan berubah')
                    // ->modalSubmitActionLabel('Simpan')
                    ->action(fn (Collection $records, array $data) => $records->each(
                        function ($record) {

                            User::where('username', $record->username)
                                ->update(['panelrole' => 'pengajar']);

                            Notification::make()
                                // ->success()
                                ->title('Status User berhasil diubah')
                                ->icon('heroicon-o-exclamation-triangle')
                                ->iconColor('success')
                                // ->persistent()
                                ->color('success')
                                ->send();
                        }
                    ))
                    ->deselectRecordsAfterCompletion(),

                BulkAction::make('Set Panel Walisantri')
                    ->label(__('Set Panel Walisantri'))
                    ->color('warning')
                    // ->requiresConfirmation()
                    // ->modalIcon('heroicon-o-check-circle')
                    // ->modalIconColor('success')
                    // ->modalHeading('Ubah Status menjadi "Diterima sebagai santri?"')
                    // ->modalDescription('Setelah klik tombol "Simpan", maka status akan berubah')
                    // ->modalSubmitActionLabel('Simpan')
                    ->action(fn (Collection $records, array $data) => $records->each(
                        function ($record) {

                            User::where('username', $record->username)
                                ->update(['panelrole' => 'walisantri']);

                            Notification::make()
                                // ->success()
                                ->title('Status User berhasil diubah')
                                ->icon('heroicon-o-exclamation-triangle')
                                ->iconColor('success')
                                // ->persistent()
                                ->color('success')
                                ->send();
                        }
                    ))
                    ->deselectRecordsAfterCompletion(),

                BulkAction::make('Set Panel PSB')
                    ->label(__('Set Panel PSB'))
                    ->color('warning')
                    // ->requiresConfirmation()
                    // ->modalIcon('heroicon-o-check-circle')
                    // ->modalIconColor('success')
                    // ->modalHeading('Ubah Status menjadi "Diterima sebagai santri?"')
                    // ->modalDescription('Setelah klik tombol "Simpan", maka status akan berubah')
                    // ->modalSubmitActionLabel('Simpan')
                    ->action(fn (Collection $records, array $data) => $records->each(
                        function ($record) {

                            User::where('username', $record->username)
                                ->update(['panelrole' => 'psb']);

                            Notification::make()
                                // ->success()
                                ->title('Status User berhasil diubah')
                                ->icon('heroicon-o-exclamation-triangle')
                                ->iconColor('success')
                                // ->persistent()
                                ->color('success')
                                ->send();
                        }
                    ))
                    ->deselectRecordsAfterCompletion(),


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
            'index' => Pages\ListTsnuniques::route('/'),
            'create' => Pages\CreateTsnunique::route('/create'),
            'view' => Pages\ViewTsnunique::route('/{record}'),
            'edit' => Pages\EditTsnunique::route('/{record}/edit'),
        ];
    }
}
