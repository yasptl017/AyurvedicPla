<?php

namespace App\Filament\Resources\TimeOfAdministrationResource\Pages;

use App\Filament\Resources\TimeOfAdministrationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTimeOfAdministrations extends ListRecords
{
    protected static string $resource = TimeOfAdministrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
