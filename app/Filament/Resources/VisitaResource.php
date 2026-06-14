<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitaResource\Pages;
use App\Models\Evento;
use App\Models\PadreEspiritual;
use App\Models\Visita;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VisitaResource extends Resource
{
    protected static ?string $model = Visita::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationLabel = 'Visitas';

    protected static ?string $modelLabel = 'visita';

    protected static ?string $pluralModelLabel = 'Visitas';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos del visitante')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre completo')
                            ->required()
                            ->maxLength(150)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono / WhatsApp')
                            ->tel()
                            ->placeholder('Ej. 3312345678'),
                        Forms\Components\TextInput::make('ujier_nombre')
                            ->label('Registrado por (ujier)')
                            ->maxLength(150),
                        Forms\Components\Select::make('necesidad')
                            ->label('Tipo de necesidad')
                            ->options(collect(Visita::NECESIDADES)->map(fn ($n) => $n['emoji'] . ' ' . $n['label'] . ' (' . $n['color'] . ')')->toArray())
                            ->native(false),
                    ]),

                Forms\Components\Section::make('Seguimiento')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('evento_id')
                            ->label('Evento')
                            ->relationship('evento', 'nombre')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nombre')->required(),
                                Forms\Components\DatePicker::make('fecha'),
                            ]),
                        Forms\Components\Select::make('estatus')
                            ->label('Estatus de seguimiento')
                            ->options([
                                'nuevo' => 'Nuevo',
                                'contactado' => 'Contactado',
                                'integrado' => 'Integrado a comunidad',
                                'sin_respuesta' => 'Sin respuesta',
                            ])
                            ->default('nuevo')
                            ->required(),
                        Forms\Components\Select::make('padre_espiritual_id')
                            ->label('Padre/Madre espiritual')
                            ->relationship('padreEspiritual', 'nombre')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nombre')->required(),
                                Forms\Components\TextInput::make('telefono')->tel(),
                            ]),
                        Forms\Components\TextInput::make('padre_espiritual_otro')
                            ->label('Si no aparece en la lista, escribe el nombre')
                            ->maxLength(150),
                        Forms\Components\Textarea::make('notas')
                            ->label('Notas / observaciones')
                            ->columnSpanFull()
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Visitante')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('necesidad')
                    ->label('Necesidad')
                    ->placeholder('—')
                    ->formatStateUsing(fn (?string $state) => $state ? (Visita::NECESIDADES[$state]['emoji'] . ' ' . Visita::NECESIDADES[$state]['label']) : '—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ujier_nombre')
                    ->label('Ujier')
                    ->placeholder('—')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('evento.nombre')
                    ->label('Evento')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('nombre_padre')
                    ->label('Padre/Madre Espiritual')
                    ->state(fn (Visita $record): string => $record->nombrePadreEspiritual())
                    ->searchable(query: function ($query, string $search) {
                        $query->whereHas('padreEspiritual', fn ($q) => $q->where('nombre', 'like', "%{$search}%"))
                            ->orWhere('padre_espiritual_otro', 'like', "%{$search}%");
                    }),
                Tables\Columns\SelectColumn::make('estatus')
                    ->label('Estatus')
                    ->options([
                        'nuevo' => 'Nuevo',
                        'contactado' => 'Contactado',
                        'integrado' => 'Integrado',
                        'sin_respuesta' => 'Sin respuesta',
                    ]),
                Tables\Columns\TextColumn::make('seguimiento')
                    ->label('Seguimiento')
                    ->state(function (Visita $record): ?int {
                        return $record->estatus === 'nuevo' ? $record->created_at->diffInDays(now()) : null;
                    })
                    ->formatStateUsing(function (?int $state, Visita $record) {
                        if ($record->estatus !== 'nuevo') {
                            return '✓ Atendido';
                        }

                        return $state === 0 ? 'Pendiente: hoy' : "Pendiente: {$state} día(s)";
                    })
                    ->badge()
                    ->color(function (?int $state, Visita $record) {
                        if ($record->estatus !== 'nuevo') {
                            return 'success';
                        }

                        return match (true) {
                            $state >= 2 => 'danger',
                            $state >= 1 => 'warning',
                            default => 'gray',
                        };
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('evento_id')
                    ->label('Evento')
                    ->relationship('evento', 'nombre'),
                Tables\Filters\SelectFilter::make('padre_espiritual_id')
                    ->label('Padre/Madre Espiritual')
                    ->relationship('padreEspiritual', 'nombre'),
                Tables\Filters\SelectFilter::make('estatus')
                    ->options([
                        'nuevo' => 'Nuevo',
                        'contactado' => 'Contactado',
                        'integrado' => 'Integrado',
                        'sin_respuesta' => 'Sin respuesta',
                    ]),
                Tables\Filters\Filter::make('seguimiento_atrasado')
                    ->label('Pendientes (1+ día sin contactar)')
                    ->query(fn ($query) => $query->where('estatus', 'nuevo')->where('created_at', '<=', now()->subDay()))
                    ->toggle(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('exportar')
                    ->label('Exportar CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function (Tables\Contracts\HasTable $livewire) {
                        $visitas = $livewire->getFilteredTableQuery()
                            ->with(['evento', 'padreEspiritual'])
                            ->orderBy('created_at')
                            ->get();

                        return response()->streamDownload(function () use ($visitas) {
                            $handle = fopen('php://output', 'w');
                            fputcsv($handle, ['Fecha', 'Nombre', 'Teléfono', 'Evento', 'Padre/Madre Espiritual', 'Estatus', 'Notas', 'Ujier', 'Necesidad']);

                            foreach ($visitas as $visita) {
                                fputcsv($handle, [
                                    $visita->created_at->format('d/m/Y H:i'),
                                    $visita->nombre,
                                    $visita->telefono,
                                    $visita->evento?->nombre,
                                    $visita->nombrePadreEspiritual(),
                                    $visita->estatus,
                                    $visita->notas,
                                    $visita->ujier_nombre,
                                    $visita->necesidad ? Visita::NECESIDADES[$visita->necesidad]['color'] : '',
                                ]);
                            }

                            fclose($handle);
                        }, 'visitas-'.now()->format('Y-m-d_His').'.csv');
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->visible(fn (Visita $record) => filled($record->telefono))
                    ->url(function (Visita $record) {
                        $tel = preg_replace('/\D/', '', $record->telefono);
                        if (strlen($tel) === 10) {
                            $tel = '52'.$tel;
                        }
                        $texto = '¡Hola '.$record->nombre.'! Gracias por visitarnos, fue una bendición tenerte con nosotros 🙌';

                        return 'https://wa.me/'.$tel.'?text='.urlencode($texto);
                    })
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListVisitas::route('/'),
            'create' => Pages\CreateVisita::route('/create'),
            'edit' => Pages\EditVisita::route('/{record}/edit'),
        ];
    }
}
