<?php

namespace App\Filament\App\Resources\Patients\Resources\PatientHistories\Widgets;

use App\Filament\App\Resources\Patients\Resources\PatientHistories\PatientHistoryResource;
use App\Models\PatientHistory;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PreviousHistoriesWidget extends TableWidget
{
    public ?string $patientId = null;

    public ?string $currentRecordId = null;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Previous Patient Histories';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PatientHistory::query()
                    ->where('PatientId', $this->patientId)
                    ->when($this->currentRecordId, fn (Builder $query) => $query->where('Id', '!=', $this->currentRecordId))
                    ->with(['diseases', 'symptoms', 'prescriptions'])
                    ->latest('CreatedDate')
            )
            ->columns([
                TextColumn::make('diseases.Name')
                    ->label('Diseases')
                    ->badge()
                    ->limitList(2)
                    ->separator(',')
                    ->wrap(),

                TextColumn::make('prescriptions_count')
                    ->counts('prescriptions')
                    ->label('Meds')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                TextColumn::make('ConsultationFee')
                    ->label('Fees')
                    ->numeric()
                    ->weight(FontWeight::Bold)
                    ->prefix('Consult: ')
                    ->description(fn (PatientHistory $record) => 'Meds: '.number_format($record->MedicinesFee)),

                TextColumn::make('CreatedDate')
                    ->label('Date')
                    ->dateTime('M d, Y h:i A')
                    ->sortable()
                    ->description(fn (PatientHistory $record) => $record->NextAppointmentDate
                        ? 'Next: '.$record->NextAppointmentDate->format('M d, Y')
                        : 'No follow-up'
                    ),
            ])
            ->recordActions([
                Action::make('edit')
                    ->icon(Heroicon::PencilSquare)
                    ->url(fn (PatientHistory $record): string => PatientHistoryResource::getUrl('edit', [
                        'record' => $record,
                        'patient' => $record->PatientId,
                    ])),

                Action::make('replicate')
                    ->label('Repeat')
                    ->icon('heroicon-o-document-duplicate')
                    ->requiresConfirmation()
                    ->action(function (PatientHistory $record) {
                        $newHistory = DB::transaction(function () use ($record) {
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

                        return redirect(PatientHistoryResource::getUrl('edit', ['record' => $newHistory, 'patient' => $newHistory->patient]));
                    }),

                Action::make('print')
                    ->icon(Heroicon::Printer)
                    ->color('gray')
                    ->url(fn (PatientHistory $record): string => route('order.print', $record))
                    ->openUrlInNewTab(),

                DeleteAction::make(),
            ])
            ->emptyStateHeading('No previous histories')
            ->emptyStateDescription('This patient has no previous history entries.')
            ->paginated([5, 10, 25]);
    }
}
