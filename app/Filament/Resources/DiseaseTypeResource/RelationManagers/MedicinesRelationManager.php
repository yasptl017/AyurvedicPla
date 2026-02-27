<?php

namespace App\Filament\Resources\DiseaseTypeResource\RelationManagers;

use App\Filament\Resources\MedicineResource;
use App\Models\Anupana;
use App\Models\Medicine;
use App\Models\TimeOfAdministration;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MedicinesRelationManager extends RelationManager
{
    protected static string $relationship = 'medicines';

    protected static ?string $relatedResource = MedicineResource::class;

    protected static string|null|BackedEnum $icon = Heroicon::OutlinedQueueList;

    protected static ?string $label = 'Medicines';

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
                Action::make('attachMedicines')
                    ->label('Attach Medicines')
                    ->icon(Heroicon::OutlinedPaperClip)
                    ->form([
                        CheckboxList::make('medicines')
                            ->options(fn () => Medicine::query()->pluck('Name', 'Id'))
                            ->default(fn () => $this->getOwnerRecord()->medicines()->pluck('Medicines.Id')->toArray())
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(2),
                        Select::make('TimeOfAdministrationId')
                            ->label('Time of Administration')
                            ->options(fn () => TimeOfAdministration::query()->pluck('Name', 'Id'))
                            ->searchable()
                            ->required(),
                        Select::make('AnupanaId')
                            ->label('Anupana')
                            ->options(fn () => Anupana::query()->pluck('Name', 'Id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->modalWidth('3xl')
                    ->stickyModalFooter()
                    ->action(function (array $data): void {
                        $userId = auth()->id();
                        $now = now();

                        $this->getOwnerRecord()->medicines()->syncWithPivotValues(
                            $data['medicines'],
                            [
                                'CreatedBy' => $userId,
                                'ModifiedBy' => $userId,
                                'DeletedBy' => '00000000-0000-0000-0000-000000000000',
                                'IsDeleted' => false,
                                'CreatedDate' => $now,
                                'ModifiedDate' => $now,
                                'TimeOfAdministrationId' => $data['TimeOfAdministrationId'],
                                'AnupanaId' => $data['AnupanaId'],
                            ]
                        );
                    }),
            ])->recordActions([
                EditAction::make(),
                DetachAction::make(),
            ]);
    }
}
