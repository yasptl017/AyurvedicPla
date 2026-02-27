<?php

namespace App\Filament\Resources\DiseaseTypeResource\Pages;

use App\Filament\Resources\DiseaseTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDiseaseType extends CreateRecord
{
    protected static string $resource = DiseaseTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
