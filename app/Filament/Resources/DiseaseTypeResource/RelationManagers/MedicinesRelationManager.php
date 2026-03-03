<?php

namespace App\Filament\Resources\DiseaseTypeResource\RelationManagers;

use App\Models\Anupana;
use App\Models\Medicine;
use App\Models\MedicineForm;
use App\Models\TimeOfAdministration;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DetachAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MedicinesRelationManager extends RelationManager
{
    protected static string $relationship = 'medicines';

    protected static string|null|BackedEnum $icon = Heroicon::OutlinedQueueList;

    protected static ?string $label = 'Medicines';

    protected ?array $medicineOptions = null;

    protected ?array $timeOptions = null;

    protected ?array $anupanaOptions = null;

    protected ?array $medicineFormOptions = null;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Name')
                    ->label('Medicine')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('medicineForm.Name')
                    ->label('Form')
                    ->sortable(),
                TextColumn::make('CompanyName')
                    ->label('Company')
                    ->wrap(),
                TextColumn::make('pivot.Dose')
                    ->label('Dose')
                    ->wrap(),
                TextColumn::make('pivot.TimeOfAdministrationId')
                    ->label('Time Of Administration')
                    ->formatStateUsing(fn (mixed $state): string => $this->getTimeOptions()[$state] ?? '-'),
                TextColumn::make('pivot.Duration')
                    ->label('Quantity')
                    ->wrap(),
                TextColumn::make('pivot.AnupanaId')
                    ->label('Anupana')
                    ->formatStateUsing(fn (mixed $state): string => $this->getAnupanaOptions()[$state] ?? '-'),
            ])
            ->headerActions([
                Action::make('createMedicine')
                    ->label('New Medicine')
                    ->form([
                        TextInput::make('Name')
                            ->label('Medicine Name')
                            ->required(),
                        Select::make('MedicineFormId')
                            ->label('Medicine Form')
                            ->options(fn (): array => $this->getMedicineFormOptions())
                            ->searchable()
                            ->required(),
                        TextInput::make('CompanyName')
                            ->label('Company Name'),
                        ...$this->getMedicineDetailFields(),
                    ])
                    ->modalWidth('3xl')
                    ->stickyModalFooter()
                    ->action(function (array $data): void {
                        $medicine = Medicine::query()->create([
                            'Name' => $data['Name'],
                            'MedicineFormId' => $data['MedicineFormId'],
                            'CompanyName' => $data['CompanyName'] ?? null,
                            'IsPattern' => false,
                            'IsSpecial' => true,
                            'ClinicId' => Filament::getTenant()?->Id,
                        ]);

                        $this->getOwnerRecord()->medicines()->syncWithoutDetaching([
                            $medicine->Id => $this->buildNewPivotData($data),
                        ]);
                    }),
                Action::make('attachMedicines')
                    ->label('Attach Medicines')
                    ->icon(Heroicon::OutlinedPaperClip)
                    ->form([
                        CheckboxList::make('medicines')
                            ->options(fn (): array => $this->getMedicineOptions())
                            ->searchable()
                            ->required()
                            ->bulkToggleable()
                            ->columns(2),
                        ...$this->getMedicineDetailFields(),
                    ])
                    ->modalWidth('3xl')
                    ->stickyModalFooter()
                    ->action(function (array $data): void {
                        $medicineIds = collect($data['medicines'] ?? [])
                            ->filter()
                            ->map(fn ($id) => (int) $id)
                            ->unique()
                            ->values()
                            ->all();

                        if ($medicineIds === []) {
                            return;
                        }

                        $attachedMedicineIds = $this->getOwnerRecord()->medicines()
                            ->pluck('Medicines.Id')
                            ->map(fn ($id) => (int) $id)
                            ->all();

                        $attachedLookup = array_flip($attachedMedicineIds);
                        $newPivotData = $this->buildNewPivotData($data);
                        $existingPivotData = $this->buildExistingPivotData($data);
                        $toAttach = [];

                        foreach ($medicineIds as $medicineId) {
                            if (isset($attachedLookup[$medicineId])) {
                                $this->getOwnerRecord()->medicines()->updateExistingPivot($medicineId, $existingPivotData);
                                continue;
                            }

                            $toAttach[$medicineId] = $newPivotData;
                        }

                        if ($toAttach !== []) {
                            $this->getOwnerRecord()->medicines()->syncWithoutDetaching($toAttach);
                        }
                    }),
            ])->recordActions([
                Action::make('editDetails')
                    ->label('Edit Details')
                    ->fillForm(fn (Medicine $record): array => [
                        'Dose' => $record->pivot?->Dose,
                        'TimeOfAdministrationId' => $record->pivot?->TimeOfAdministrationId,
                        'Duration' => $record->pivot?->Duration,
                        'AnupanaId' => $record->pivot?->AnupanaId,
                    ])
                    ->form([
                        ...$this->getMedicineDetailFields(),
                    ])
                    ->action(function (Medicine $record, array $data): void {
                        $this->getOwnerRecord()->medicines()->updateExistingPivot(
                            $record->Id,
                            $this->buildExistingPivotData($data),
                        );
                    }),
                DetachAction::make(),
            ]);
    }

    protected function getMedicineOptions(): array
    {
        if ($this->medicineOptions !== null) {
            return $this->medicineOptions;
        }

        return $this->medicineOptions = Medicine::query()
            ->orderBy('Name')
            ->pluck('Name', 'Id')
            ->toArray();
    }

    protected function getTimeOptions(): array
    {
        if ($this->timeOptions !== null) {
            return $this->timeOptions;
        }

        return $this->timeOptions = TimeOfAdministration::query()
            ->orderBy('Name')
            ->pluck('Name', 'Id')
            ->toArray();
    }

    protected function getAnupanaOptions(): array
    {
        if ($this->anupanaOptions !== null) {
            return $this->anupanaOptions;
        }

        return $this->anupanaOptions = Anupana::query()
            ->orderBy('Name')
            ->pluck('Name', 'Id')
            ->toArray();
    }

    protected function getMedicineFormOptions(): array
    {
        if ($this->medicineFormOptions !== null) {
            return $this->medicineFormOptions;
        }

        return $this->medicineFormOptions = MedicineForm::query()
            ->orderBy('Name')
            ->pluck('Name', 'Id')
            ->toArray();
    }

    /**
     * @return array<int, Select|TextInput>
     */
    protected function getMedicineDetailFields(): array
    {
        return [
            TextInput::make('Dose')
                ->required(),
            Select::make('TimeOfAdministrationId')
                ->label('Time Of Administration')
                ->options(fn (): array => $this->getTimeOptions())
                ->searchable()
                ->required(),
            TextInput::make('Duration')
                ->label('Quantity')
                ->required(),
            Select::make('AnupanaId')
                ->label('Anupana')
                ->options(fn (): array => $this->getAnupanaOptions())
                ->searchable()
                ->required(),
        ];
    }

    protected function buildExistingPivotData(array $data): array
    {
        return [
            'Dose' => $data['Dose'],
            'TimeOfAdministrationId' => $data['TimeOfAdministrationId'],
            'AnupanaId' => $data['AnupanaId'],
            'Duration' => $data['Duration'],
            'ModifiedBy' => auth()->id(),
            'ModifiedDate' => now(),
        ];
    }

    protected function buildNewPivotData(array $data): array
    {
        $timestamp = now();

        return [
            ...$this->buildExistingPivotData($data),
            'CreatedBy' => auth()->id(),
            'CreatedDate' => $timestamp,
            'DeletedBy' => '00000000-0000-0000-0000-000000000000',
            'IsDeleted' => false,
            'IsSpecial' => false,
        ];
    }
}
