<?php

namespace App\Filament\Resources\DiseaseInvestigationsResource\Pages;

use App\Filament\Resources\DiseaseInvestigationsResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditDiseaseInvestigations extends EditRecord
{
    protected static string $resource = DiseaseInvestigationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
