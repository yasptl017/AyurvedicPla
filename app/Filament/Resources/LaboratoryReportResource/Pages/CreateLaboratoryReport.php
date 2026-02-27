<?php

namespace App\Filament\Resources\LaboratoryReportResource\Pages;

use App\Filament\Resources\LaboratoryReportResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLaboratoryReport extends CreateRecord
{
    protected static string $resource = LaboratoryReportResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
