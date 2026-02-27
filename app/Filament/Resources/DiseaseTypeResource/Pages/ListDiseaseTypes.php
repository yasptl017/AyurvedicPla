<?php

namespace App\Filament\Resources\DiseaseTypeResource\Pages;

use App\Filament\Resources\DiseaseTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDiseaseTypes extends ListRecords
{
    protected static string $resource = DiseaseTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
