<?php

namespace App\Filament\App\Resources\Patients\Resources\PatientHistories\Pages;

use App\Filament\App\Resources\Patients\Resources\PatientHistories\PatientHistoryResource;
use App\Models\PatientHistory;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditPatientHistory extends EditRecord
{
    protected static string $resource = PatientHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
            Action::make('print')
                ->button()
                ->color('gray')
                ->icon(Heroicon::Printer)
                ->url(fn (PatientHistory $record): string => route('order.print', $record))
                ->openUrlInNewTab(),
        ];
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
