<?php

namespace App\Filament\Resources\MedicineResource\Pages;

use App\Filament\Resources\MedicineResource;
use App\Models\Medicine;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;

class CreateMedicine extends CreateRecord
{
    protected static string $resource = MedicineResource::class;

    protected array $medicineDetails = [];

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $mode = $data['medicine_mode'] ?? 'existing';

        if ($mode === 'new') {
            $medicine = Medicine::create([
                'Name' => $data['new_medicine_name'],
                'MedicineFormId' => $data['new_medicine_form_id'],
                'CompanyName' => $data['new_medicine_company'] ?? null,
                'IsSpecial' => true,
                'IsPattern' => false,
                'ClinicId' => Filament::getTenant()?->Id,
            ]);

            $data['MedicineId'] = $medicine->Id;
            $this->medicineDetails = [];
        } else {
            $this->medicineDetails = Arr::only($data, [
                'MedicineId',
                'MedicineFormId',
                'CompanyName',
            ]);
        }

        unset(
            $data['DiseaseId'],
            $data['MedicineFormId'],
            $data['CompanyName'],
            $data['medicine_mode'],
            $data['new_medicine_name'],
            $data['new_medicine_form_id'],
            $data['new_medicine_company'],
        );

        return $data;
    }

    protected function afterCreate(): void
    {
        if (! $this->record) {
            return;
        }

        if (empty($this->medicineDetails['MedicineId'])) {
            return;
        }

        Medicine::query()
            ->where('Id', $this->medicineDetails['MedicineId'])
            ->update([
                'MedicineFormId' => $this->medicineDetails['MedicineFormId'] ?? null,
                'CompanyName' => $this->medicineDetails['CompanyName'] ?? null,
            ]);
    }
}
