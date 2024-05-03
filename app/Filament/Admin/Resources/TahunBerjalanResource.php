<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TahunBerjalanResource\Pages;
use App\Filament\Admin\Resources\TahunBerjalanResource\RelationManagers;
use App\Models\TahunBerjalan;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TahunBerjalanResource extends Resource
{
    public static function canViewAny(): bool
    {
        return auth()->user()->id == 1;
    }

    protected static ?string $navigationGroup = 'TSN Configs';

    protected static ?int $navigationSort = 98010;

    protected static ?string $modelLabel = 'Tahun Berjalan';

    protected static ?string $navigationLabel = 'Tahun Berjalan';

    protected static ?string $pluralModelLabel = 'Tahun Berjalan';

    protected static ?string $model = TahunBerjalan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('tb')
                    ->maxLength(255),
                TextInput::make('ts')
                    ->maxLength(255),
                Checkbox::make('is_active')
                    ->label('Status'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextInputColumn::make('tb')
                    ->searchable(),
                TextInputColumn::make('ts')
                    ->searchable(),
                CheckboxColumn::make('is_active')
                    ->searchable(),
            ])
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
            'index' => Pages\ListTahunBerjalans::route('/'),
            'create' => Pages\CreateTahunBerjalan::route('/create'),
            'view' => Pages\ViewTahunBerjalan::route('/{record}'),
            'edit' => Pages\EditTahunBerjalan::route('/{record}/edit'),
        ];
    }
}
