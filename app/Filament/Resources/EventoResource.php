<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventoResource\Pages;
use App\Filament\Resources\EventoResource\RelationManagers;
use App\Models\Evento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventoResource extends Resource
{
    protected static ?string $model = Evento::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Eventos';

    protected static ?string $modelLabel = 'evento';

    protected static ?string $pluralModelLabel = 'Eventos';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre del evento')
                    ->required()
                    ->maxLength(150),
                Forms\Components\DatePicker::make('fecha')
                    ->label('Fecha'),
                Forms\Components\Toggle::make('activo')
                    ->label('Activo')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\IconColumn::make('activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('visitas_count')
                    ->label('Visitas')
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
            'index' => Pages\ManageEventos::route('/'),
        ];
    }
}
