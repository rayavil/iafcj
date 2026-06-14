<?php

namespace App\Filament\Resources\PadreEspiritualResource\Pages;

use App\Filament\Resources\PadreEspiritualResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPadreEspirituals extends ListRecords
{
    protected static string $resource = PadreEspiritualResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
