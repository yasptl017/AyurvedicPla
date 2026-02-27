<?php

namespace App\Filament\App\Resources\Patients\Resources\PatientHistories\Pages;

use App\Filament\App\Resources\Patients\PatientResource;
use App\Filament\App\Resources\Patients\Resources\PatientHistories\PatientHistoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePatientHistory extends CreateRecord
{
    protected static string $resource = PatientHistoryResource::class;

    protected function getRedirectUrl(): string
    {
        $patientId = $this->getRecord();

        return PatientResource::getUrl('edit', [
            'record' => $patientId->PatientId,
            'relation' => '0',
        ]);
    }

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = parent::getBreadcrumbs();

        // Find and update the Patient edit page breadcrumb to use relation=0
        foreach ($breadcrumbs as $url => $label) {
            if (str_contains($url, 'relation=patientHistories')) {
                $newUrl = str_replace('relation=patientHistories', 'relation=0', $url);
                unset($breadcrumbs[$url]);
                $breadcrumbs[$newUrl] = $label;
                break;
            }
        }

        return $breadcrumbs;
    }
}
