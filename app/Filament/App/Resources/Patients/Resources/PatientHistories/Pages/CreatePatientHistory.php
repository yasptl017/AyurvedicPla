<?php

namespace App\Filament\App\Resources\Patients\Resources\PatientHistories\Pages;

use App\Filament\App\Resources\Patients\PatientResource;
use App\Filament\App\Resources\Patients\Resources\PatientHistories\PatientHistoryResource;
use App\Models\Patient;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;

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
            $this->getSaveAndEditAction('saveHeader'),
            $this->getPrintAction('printHeader'),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveAndEditAction('save'),
            $this->getPrintAction('printForm'),

            $this->getCreateFormAction()
                ->label('Create'),

            $this->getCancelFormAction(),
        ];
    }

    protected function getSaveAndEditAction(string $name): Action
    {
        return Action::make($name)
            ->label('Save')
            ->color('primary')
            ->action('saveAndEdit');
    }

    protected function getPrintAction(string $name): Action
    {
        return Action::make($name)
            ->label('Print')
            ->color('gray')
            ->icon(Heroicon::Printer)
            ->disabled(fn (): bool => blank($this->getRecord()))
            ->url(fn (): ?string => $this->getRecord() ? route('order.print', $this->getRecord()) : null)
            ->openUrlInNewTab();
    }

    public function saveAndEdit(): void
    {
        $this->create(another: false);

        if (! $this->getRecord()) {
            return;
        }

        $this->redirect(
            PatientHistoryResource::getUrl('edit', [
                'record' => $this->getRecord(),
                'patient' => $this->getRecord()->PatientId,
            ])
        );
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

    protected function afterCreate(): void
    {
        $this->syncPatientComplaint();
    }

    protected function syncPatientComplaint(): void
    {
        $patientId = $this->getRecord()?->PatientId;

        if (! $patientId) {
            return;
        }

        $complainOf = data_get($this->form->getRawState(), 'patient_complain_of');

        Patient::query()
            ->where('Id', $patientId)
            ->update([
                'complain_of' => filled($complainOf) ? trim((string) $complainOf) : null,
            ]);
    }
}
