<?php

namespace App\Filament\App\Resources\Patients\Resources\PatientHistories\Pages;

use App\Filament\App\Resources\Patients\PatientResource;
use App\Filament\App\Resources\Patients\Resources\PatientHistories\PatientHistoryResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreatePatientHistory extends CreateRecord
{
    protected static string $resource = PatientHistoryResource::class;

    protected function getRedirectUrl(): string
    {
        $record = $this->getRecord();

        return PatientResource::getUrl('edit', [
            'record' => $record->PatientId,
            'relation' => '0',
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->button()
                ->color('gray')
                ->icon('heroicon-o-printer')
                ->disabled(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->color('primary')
                ->action(function () {
                    $this->create(another: false);
                    if ($this->getRecord()) {
                        $this->redirect(
                            PatientHistoryResource::getUrl('edit', [
                                'record' => $this->getRecord(),
                                'patient' => $this->getRecord()->PatientId,
                            ])
                        );
                    }
                }),

            $this->getCreateFormAction()
                ->label('Create'),

            $this->getCancelFormAction(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = parent::getBreadcrumbs();

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
