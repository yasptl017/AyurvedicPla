<?php

namespace App\Filament\Resources\DiseaseResource\RelationManagers;

use App\Filament\Resources\SymptomResource;
use App\Models\Symptom;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SymptomsRelationManager extends RelationManager
{
    protected static string $relationship = 'symptoms';

    protected static string|BackedEnum|null $icon = Heroicon::OutlinedFaceSmile;

    protected static ?string $label = 'Symptoms';

    protected static ?string $relatedResource = SymptomResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
                Action::make('attachSymptoms')
                    ->label('Attach Symptoms')
                    ->icon(Heroicon::OutlinedPaperClip)
                    ->form([
                        CheckboxList::make('symptoms')
                            ->options(fn () => Symptom::query()->pluck('Name', 'Id')->toArray())
                            ->default(fn () => $this->getOwnerRecord()->symptoms()->pluck('Symptoms.Id')->toArray())
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(2),
                    ])
                    ->modalWidth('3xl')
                    ->stickyModalFooter()
                    ->action(function (array $data): void {
                        $symptomIds = $data['symptoms'] ?? [];
                        $userId = auth()->id();
                        $now = now();

                        $this->getOwnerRecord()->symptoms()->syncWithPivotValues(
                            $symptomIds,
                            [
                                'IsMain' => true,
                                'CreatedBy' => $userId,
                                'ModifiedBy' => $userId,
                                'DeletedBy' => '00000000-0000-0000-0000-000000000000',
                                'IsDeleted' => false,
                                'CreatedDate' => $now,
                                'ModifiedDate' => $now,
                            ]
                        );
                    }),
            ])->recordActions([
                EditAction::make(),
                DetachAction::make(),
            ]);
    }
}
