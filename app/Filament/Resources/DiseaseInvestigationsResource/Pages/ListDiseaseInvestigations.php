<?php

namespace App\Filament\Resources\DiseaseInvestigationsResource\Pages;

use App\Filament\Resources\DiseaseInvestigationsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDiseaseInvestigations extends ListRecords
{
    protected static string $resource = DiseaseInvestigationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
