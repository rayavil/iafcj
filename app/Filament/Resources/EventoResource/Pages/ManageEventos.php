<?php

namespace App\Filament\Resources\EventoResource\Pages;

use App\Filament\Resources\EventoResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageEventos extends ManageRecords
{
    protected static string $resource = EventoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
