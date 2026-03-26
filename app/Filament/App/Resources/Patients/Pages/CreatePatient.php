<?php

namespace App\Filament\App\Resources\Patients\Pages;

use App\Filament\App\Pages\AwaitingPatient as AwaitingPatientPage;
use App\Filament\App\Resources\Patients\PatientResource;
use App\Models\AwaitingPatientEntry;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;

    protected function getCreateAnotherFormAction(): Action
    {
        return Action::make('createAndAddToWaitingList')
            ->label('Add to Waiting List')
            ->action('createAndAddToWaitingList')
            ->color('gray');
    }

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label('Next')
            ->submit('create')
            ->keyBindings(['mod+s']);
    }

    protected function getRedirectUrl(): string
    {
        return PatientResource::getUrl('edit', [
            'record' => $this->record,
            'relation' => 0,
        ]);
    }

    public function createAndAddToWaitingList(): void
    {
        $this->create(another: false);

        if (! $this->getRecord()) {
            return;
        }

        AwaitingPatientEntry::addForClinicAndPatient(
            clinicId: Filament::getTenant()?->Id ?? $this->getRecord()->ClinicId,
            patientId: $this->getRecord()->Id,
        );

        $this->redirect(AwaitingPatientPage::getUrl());
    }
}
