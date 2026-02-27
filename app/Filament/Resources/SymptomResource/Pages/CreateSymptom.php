<?php

namespace App\Filament\Resources\SymptomResource\Pages;

use App\Filament\Resources\SymptomResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSymptom extends CreateRecord
{
    protected static string $resource = SymptomResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
