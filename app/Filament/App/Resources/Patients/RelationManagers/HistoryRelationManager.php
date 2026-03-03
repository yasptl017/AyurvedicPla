<?php

namespace App\Filament\App\Resources\Patients\RelationManagers;

use App\Filament\App\Resources\Patients\Resources\PatientHistories\PatientHistoryResource;
use App\Models\PatientHistory;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentView;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class HistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'patientHistories';

    protected static ?string $relatedResource = PatientHistoryResource::class;

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                View::make('filament.app.relation-managers.patient-histories-list')
                    ->viewData([
                        'histories' => $this->getHistoryRecords(),
                        'ownerRecord' => $this->getOwnerRecord(),
                    ]),
            ]);
    }

    /**
     * @return Collection<int, PatientHistory>
     */
    protected function getHistoryRecords(): Collection
    {
        return $this->getOwnerRecord()
            ->patientHistories()
            ->with([
                'patient:Id,complain_of,history_of',
                'diseases:Id,Name',
                'symptoms:Id,Name',
                'modernSymptoms:Id,Name',
                'prescriptions.medicine:Id,Name',
                'vital',
                'womenHistory',
                'panchakarmas:Id,Name',
                'rogaPariksa',
                'hetuPariksa',
                'patientRecords',
                'sketches',
                'captures',
                'patientFiles',
            ])
            ->latest('CreatedDate')
            ->get();
    }

    public function repeatHistory(int|string $historyId): void
    {
        /** @var PatientHistory $record */
        $record = $this->getOwnerRecord()
            ->patientHistories()
            ->with([
                'prescriptions',
                'diseases',
                'symptoms',
                'modernSymptoms',
                'panchakarmas',
                'womenHistory',
                'vital',
                'rogaPariksa',
                'hetuPariksa',
                'patientFiles',
                'sketches',
                'captures',
            ])
            ->findOrFail($historyId);

        $newHistory = DB::transaction(function () use ($record): PatientHistory {
            $newHistory = $record->replicate(['prescriptions_count']);
            $newHistory->CreatedDate = now();
            $newHistory->save();

            foreach ($record->prescriptions as $prescription) {
                $newHistory->prescriptions()->save($prescription->replicate());
            }

            $diseases = $record->diseases()->withPivot(['DiseaseTypeId'])->get()
                ->mapWithKeys(fn ($item) => [
                    $item->Id => ['DiseaseTypeId' => $item->pivot->DiseaseTypeId],
                ]);
            $newHistory->diseases()->attach($diseases);

            $newHistory->symptoms()->attach($record->symptoms()->pluck('SymptomId'));
            $newHistory->modernSymptoms()->attach($record->modernSymptoms()->pluck('ModernSymptoms.Id'));

            $panchakarmas = $record->panchakarmas()->withPivot(['Detail'])->get()
                ->mapWithKeys(fn ($item) => [
                    $item->Id => ['Detail' => $item->pivot->Detail],
                ]);
            $newHistory->panchakarmas()->attach($panchakarmas);

            $relations = ['womenHistory', 'vital', 'rogaPariksa', 'hetuPariksa'];

            foreach ($relations as $relation) {
                if ($record->$relation) {
                    $newHistory->$relation()->save($record->$relation->replicate());
                }
            }

            foreach ($record->patientFiles as $file) {
                $newHistory->patientFiles()->create($file->only(['File']));
            }

            foreach ($record->sketches as $sketch) {
                $newHistory->sketches()->create($sketch->only(['sketch']));
            }

            foreach ($record->captures as $capture) {
                $newHistory->captures()->create($capture->only(['capture']));
            }

            return $newHistory;
        });

        $url = PatientHistoryResource::getUrl('edit', [
            'record' => $newHistory->Id,
            'patient' => $this->getOwnerRecord()->Id,
        ]);

        $this->redirect($url, navigate: FilamentView::hasSpaMode($url));
    }
}
