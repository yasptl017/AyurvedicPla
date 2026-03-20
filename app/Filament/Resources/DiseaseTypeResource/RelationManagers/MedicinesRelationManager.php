<?php

namespace App\Filament\Resources\DiseaseTypeResource\RelationManagers;

use App\Models\Anupana;
use App\Models\DiseaseTypeMedicine;
use App\Models\Medicine;
use App\Models\MedicineForm;
use App\Models\TimeOfAdministration;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DetachAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
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
            ->defaultSort('diseasetypemedicines.OrderNumber', 'asc')
            ->columns([
                TextColumn::make('pivot.OrderNumber')
                    ->label('Order')
                    ->sortable(false),
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
                            ->pluck('medicines.Id')
                            ->map(fn ($id) => (int) $id)
                            ->all();

                        $attachedLookup = array_flip($attachedMedicineIds);
                        $basePivotData = $this->buildNewPivotData($data);
                        $existingPivotData = $this->buildExistingPivotData($data);
                        $toAttach = [];

                        foreach ($medicineIds as $medicineId) {
                            if (isset($attachedLookup[$medicineId])) {
                                $this->getOwnerRecord()->medicines()->updateExistingPivot($medicineId, $existingPivotData);

                                continue;
                            }

                            $toAttach[$medicineId] = $basePivotData;
                        }

                        if ($toAttach !== []) {
                            $this->getOwnerRecord()->medicines()->syncWithoutDetaching($toAttach);
                            $this->reorderAlphabetically();
                        }
                    }),
                Action::make('autoOrderAlphabetically')
                    ->label('Auto-order A→Z')
                    ->icon(Heroicon::OutlinedBarsArrowDown)
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Auto-order Medicines Alphabetically')
                    ->modalDescription('This will assign sequential order numbers to all medicines sorted A→Z by name. Any existing custom order will be overwritten.')
                    ->action(function (): void {
                        $this->reorderAlphabetically();
                    }),
                Action::make('reorderMedicines')
                    ->label('Reorder')
                    ->icon(Heroicon::OutlinedBarsArrowDown)
                    ->color('gray')
                    ->fillForm(function (): array {
                        $medicines = DiseaseTypeMedicine::query()
                            ->where('DiseaseTypeId', $this->getOwnerRecord()->Id)
                            ->whereHas('medicine')
                            ->with(['medicine' => fn ($q) => $q->select(['Id', 'Name'])])
                            ->orderByRaw('CASE WHEN OrderNumber IS NULL THEN 1 ELSE 0 END, OrderNumber ASC')
                            ->select(['Id', 'MedicineId', 'OrderNumber'])
                            ->get()
                            ->map(fn ($item) => [
                                'pivot_id' => $item->Id,
                                'medicine_name' => $item->medicine?->Name ?? '-',
                                'OrderNumber' => $item->OrderNumber >= 1 ? $item->OrderNumber : null,
                            ])
                            ->values()
                            ->toArray();

                        return ['medicines' => $medicines];
                    })
                    ->form([
                        Repeater::make('medicines')
                            ->label('Medicine Order')
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->schema([
                                Hidden::make('pivot_id'),
                                TextInput::make('medicine_name')
                                    ->label('Medicine')
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('OrderNumber')
                                    ->label('Order')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required(),
                            ])
                            ->columns(2),
                    ])
                    ->modalWidth('lg')
                    ->stickyModalFooter()
                    ->action(function (array $data): void {
                        $diseaseTypeId = $this->getOwnerRecord()->Id;
                        $now = now();
                        $userId = auth()->id();

                        // Load current order state from DB (keyed by pivot Id)
                        $current = DiseaseTypeMedicine::query()
                            ->where('DiseaseTypeId', $diseaseTypeId)
                            ->pluck('OrderNumber', 'Id')
                            ->toArray();

                        // Build map of pivot_id => new requested OrderNumber (only non-hidden items are dehydrated)
                        $requested = [];
                        foreach ($data['medicines'] as $item) {
                            $pivotId = (int) $item['pivot_id'];
                            $newOrder = $item['OrderNumber'] === null ? null : (int) $item['OrderNumber'];
                            // Clamp: never allow saving 0 or negative via this form
                            if ($newOrder !== null && $newOrder < 1) {
                                $newOrder = 1;
                            }
                            $requested[$pivotId] = $newOrder;
                        }

                        // Separate items that already had a position from those that had none (NULL).
                        // Previously unordered items are assigned directly — no shifting needed.
                        $newArrivals = [];
                        $moves = [];

                        foreach ($requested as $pivotId => $newOrder) {
                            if ($newOrder === null) {
                                continue;
                            }
                            $oldOrder = isset($current[$pivotId]) ? (int) $current[$pivotId] : null;

                            if ($oldOrder === null || $oldOrder < 1) {
                                $newArrivals[$pivotId] = $newOrder;
                            } elseif ($newOrder !== $oldOrder) {
                                $moves[$pivotId] = ['old' => $oldOrder, 'new' => $newOrder];
                            }
                        }

                        // Apply shifts for items that already had a valid order position
                        foreach ($moves as $pivotId => $move) {
                            $oldOrder = $move['old'];
                            $newOrder = $move['new'];

                            if ($newOrder < $oldOrder) {
                                // Moving up: shift items between newOrder and oldOrder-1 down by 1
                                DiseaseTypeMedicine::query()
                                    ->where('DiseaseTypeId', $diseaseTypeId)
                                    ->where('Id', '!=', $pivotId)
                                    ->where('OrderNumber', '>=', 1)
                                    ->whereBetween('OrderNumber', [$newOrder, $oldOrder - 1])
                                    ->increment('OrderNumber', 1, [
                                        'ModifiedBy' => $userId,
                                        'ModifiedDate' => $now,
                                    ]);
                            } else {
                                // Moving down: shift items between oldOrder+1 and newOrder up by 1
                                DiseaseTypeMedicine::query()
                                    ->where('DiseaseTypeId', $diseaseTypeId)
                                    ->where('Id', '!=', $pivotId)
                                    ->where('OrderNumber', '>=', 1)
                                    ->whereBetween('OrderNumber', [$oldOrder + 1, $newOrder])
                                    ->decrement('OrderNumber', 1, [
                                        'ModifiedBy' => $userId,
                                        'ModifiedDate' => $now,
                                    ]);
                            }

                            DiseaseTypeMedicine::query()
                                ->where('Id', $pivotId)
                                ->update([
                                    'OrderNumber' => $newOrder,
                                    'ModifiedBy' => $userId,
                                    'ModifiedDate' => $now,
                                ]);
                        }

                        // Directly assign order for previously-unordered medicines (no shifting)
                        foreach ($newArrivals as $pivotId => $newOrder) {
                            DiseaseTypeMedicine::query()
                                ->where('Id', $pivotId)
                                ->update([
                                    'OrderNumber' => $newOrder,
                                    'ModifiedBy' => $userId,
                                    'ModifiedDate' => $now,
                                ]);
                        }

                        // After all moves, run a final normalisation pass to close any gaps
                        // and ensure no order value falls below 1.
                        $this->normaliseOrderNumbers($diseaseTypeId, $userId, $now);
                    }),
            ])->recordActions([
                Action::make('editDetails')
                    ->label('Edit Details')
                    ->fillForm(fn (Medicine $record): array => [
                        'Dose' => $record->pivot?->Dose,
                        'TimeOfAdministrationId' => $record->pivot?->TimeOfAdministrationId,
                        'Duration' => $record->pivot?->Duration,
                        'AnupanaId' => $record->pivot?->AnupanaId,
                        'OrderNumber' => $record->pivot?->OrderNumber,
                    ])
                    ->form([
                        ...$this->getMedicineDetailFields(),
                        TextInput::make('OrderNumber')
                            ->label('Order')
                            ->numeric()
                            ->minValue(1),
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
            ->orderBy('NameGujarati')
            ->pluck('NameGujarati', 'Id')
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
        $pivotData = [
            'Dose' => $data['Dose'],
            'TimeOfAdministrationId' => $data['TimeOfAdministrationId'],
            'AnupanaId' => $data['AnupanaId'],
            'Duration' => $data['Duration'],
            'ModifiedBy' => auth()->id(),
            'ModifiedDate' => now(),
        ];

        if (array_key_exists('OrderNumber', $data)) {
            $pivotData['OrderNumber'] = $data['OrderNumber'];
        }

        return $pivotData;
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

    /**
     * Rebuild OrderNumber for all medicines of this disease type sorted alphabetically by name.
     */
    protected function reorderAlphabetically(): void
    {
        $now = now();
        $userId = auth()->id();

        $rows = DiseaseTypeMedicine::query()
            ->where('DiseaseTypeId', $this->getOwnerRecord()->Id)
            ->whereHas('medicine')
            ->with(['medicine' => fn ($q) => $q->select(['Id', 'Name'])])
            ->select(['Id', 'MedicineId'])
            ->get()
            ->sortBy(fn ($row) => strtolower($row->medicine?->Name ?? ''))
            ->values();

        foreach ($rows as $index => $row) {
            DiseaseTypeMedicine::query()
                ->where('Id', $row->Id)
                ->update([
                    'OrderNumber' => $index + 1,
                    'ModifiedBy' => $userId,
                    'ModifiedDate' => $now,
                ]);
        }
    }

    protected function getNextOrderNumber(): int
    {
        return (DiseaseTypeMedicine::query()
            ->where('DiseaseTypeId', $this->getOwnerRecord()->Id)
            ->where('OrderNumber', '>=', 1)
            ->max('OrderNumber') ?? 0) + 1;
    }

    /**
     * Re-sequence all medicines for the given disease type so there are no gaps.
     */
    protected function normaliseOrderNumbers(string $diseaseTypeId, mixed $userId, mixed $now): void
    {
        $rows = DiseaseTypeMedicine::query()
            ->where('DiseaseTypeId', $diseaseTypeId)
            ->whereHas('medicine')
            ->orderBy('OrderNumber')
            ->select(['Id', 'OrderNumber'])
            ->get();

        foreach ($rows as $index => $row) {
            $expected = $index + 1;
            if ((int) $row->OrderNumber !== $expected) {
                DiseaseTypeMedicine::query()
                    ->where('Id', $row->Id)
                    ->update([
                        'OrderNumber' => $expected,
                        'ModifiedBy' => $userId,
                        'ModifiedDate' => $now,
                    ]);
            }
        }
    }
}
