<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PadreEspiritualResource\Pages;
use App\Models\PadreEspiritual;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PadreEspiritualResource extends Resource
{
    protected static ?string $model = PadreEspiritual::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Padres Espirituales';

    protected static ?string $modelLabel = 'padre/madre espiritual';

    protected static ?string $pluralModelLabel = 'Padres Espirituales';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre completo')
                    ->required()
                    ->maxLength(150),
                Forms\Components\TextInput::make('telefono')
                    ->label('Teléfono / WhatsApp')
                    ->tel()
                    ->maxLength(30),
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
                Tables\Columns\TextColumn::make('telefono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('visitas_count')
                    ->label('Visitas asignadas')
                    ->counts('visitas')
                    ->badge()
                    ->color('success'),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount('visitas');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPadreEspirituals::route('/'),
            'create' => Pages\CreatePadreEspiritual::route('/create'),
            'edit' => Pages\EditPadreEspiritual::route('/{record}/edit'),
        ];
    }
}
