<?php

namespace App\Filament\Resources\LaboratoryReportResource\Pages;

use App\Filament\Resources\LaboratoryReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLaboratoryReports extends ListRecords
{
    protected static string $resource = LaboratoryReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
