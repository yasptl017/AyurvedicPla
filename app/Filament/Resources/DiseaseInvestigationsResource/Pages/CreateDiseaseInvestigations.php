<?php

namespace App\Filament\Resources\DiseaseInvestigationsResource\Pages;

use App\Filament\Resources\DiseaseInvestigationsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDiseaseInvestigations extends CreateRecord
{
    protected static string $resource = DiseaseInvestigationsResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
