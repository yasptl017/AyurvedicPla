<?php

namespace App\Filament\Resources\LaboratoryReportResource\Pages;

use App\Filament\Resources\LaboratoryReportResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditLaboratoryReport extends EditRecord
{
    protected static string $resource = LaboratoryReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
