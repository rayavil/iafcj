<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UjierResource\Pages;
use App\Filament\Resources\UjierResource\RelationManagers;
use App\Models\Ujier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UjierResource extends Resource
{
    protected static ?string $model = Ujier::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationLabel = 'Ujieres';

    protected static ?string $modelLabel = 'ujier';

    protected static ?string $pluralModelLabel = 'Ujieres';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre completo')
                    ->required()
                    ->maxLength(150),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('nombre')
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->weight('bold'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUjiers::route('/'),
        ];
    }
}
