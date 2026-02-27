<?php

namespace App\Filament\App\Resources\Patients\Pages;

use App\Filament\App\Resources\Patients\PatientResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()->label('Add to Waiting List');
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
}
