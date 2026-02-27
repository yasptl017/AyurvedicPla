<?php

namespace App\Filament\Resources\MedicineFormResource\Pages;

use App\Filament\Resources\MedicineFormResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMedicineForm extends CreateRecord
{
    protected static string $resource = MedicineFormResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
