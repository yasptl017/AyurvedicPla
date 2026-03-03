<?php

namespace App\Filament\App\Resources\Patients\Pages;

use App\Filament\App\Resources\Patients\PatientResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPatient extends EditRecord
{
    protected static string $resource = PatientResource::class;

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return 'Edit Patient';

    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            Action::make('next')
                ->label('Next')
                ->action('saveAndGoToHistory'),
            $this->getCancelFormAction(),
        ];
    }

    public function saveAndGoToHistory(): void
    {
        $this->save(shouldRedirect: false);

        $url = PatientResource::getUrl('edit', ['record' => $this->getRecord()])
            .'?relation=0';

        $this->redirect($url);
    }
}
