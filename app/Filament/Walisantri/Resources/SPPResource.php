<?php

namespace App\Filament\Walisantri\Resources;

use App\Filament\Walisantri\Resources\SPPResource\Pages;
use App\Filament\Walisantri\Resources\SPPResource\RelationManagers;
use App\Models\Santri;
use App\Models\SPP;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SPPResource extends Resource
{
    protected static ?int $navigationSort = 01020;

    protected static ?string $modelLabel = 'SPP';

    protected static ?string $navigationLabel = 'SPP';

    protected static ?string $pluralModelLabel = 'SPP';

    protected static ?string $model = Santri::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

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
        ->emptyStateIcon('heroicon-o-wallet')
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListSPPS::route('/'),
            'create' => Pages\CreateSPP::route('/create'),
            'view' => Pages\ViewSPP::route('/{record}'),
            'edit' => Pages\EditSPP::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {

        // return parent::getEloquentQuery()->where('qism_id', Auth::user()->mudirqism)->orWhere('');

        return parent::getEloquentQuery()->where('nama_lengkap', 'zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz');
    }
}
