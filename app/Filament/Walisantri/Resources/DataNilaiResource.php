<?php

namespace App\Filament\Walisantri\Resources;

use App\Filament\Walisantri\Resources\DataNilaiResource\Pages;
use App\Filament\Walisantri\Resources\DataNilaiResource\RelationManagers;
use App\Models\DataNilai;
use App\Models\Nilai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DataNilaiResource extends Resource
{

    protected static ?int $navigationSort = 01010;

    protected static ?string $modelLabel = 'Nilai';

    protected static ?string $navigationLabel = 'Nilai';

    protected static ?string $pluralModelLabel = 'Nilai';

    protected static ?string $model = Nilai::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

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
        ->emptyStateHeading('Segera hadir')
        ->emptyStateIcon('heroicon-o-academic-cap')
            ->columns([
                //
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
            'index' => Pages\ListDataNilais::route('/'),
            'create' => Pages\CreateDataNilai::route('/create'),
            'view' => Pages\ViewDataNilai::route('/{record}'),
            'edit' => Pages\EditDataNilai::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {

        // return parent::getEloquentQuery()->where('qism_id', Auth::user()->mudirqism)->orWhere('');

        return parent::getEloquentQuery()->where('mapel_id', 'a');
    }
}
