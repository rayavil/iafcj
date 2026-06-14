<?php

namespace App\Filament\Resources\UjierResource\Pages;

use App\Filament\Resources\UjierResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageUjiers extends ManageRecords
{
    protected static string $resource = UjierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
