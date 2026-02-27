<?php

namespace App\Filament\Resources\TimeOfAdministrationResource\Pages;

use App\Filament\Resources\TimeOfAdministrationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTimeOfAdministration extends CreateRecord
{
    protected static string $resource = TimeOfAdministrationResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
