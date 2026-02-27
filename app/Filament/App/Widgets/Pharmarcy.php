<?php

namespace App\Filament\App\Widgets;

use App\Models\PatientHistory;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Facades\Filament;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class Pharmarcy extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => PatientHistory::query()
                ->whereHas('patient', fn ($query) => $query->where('ClinicId', Filament::getTenant()->Id))
                ->whereIn(DB::raw('(PatientId, CreatedDate)'), function ($query) {
                    $query->select('PatientId', DB::raw('MAX(CreatedDate)'))
                        ->from('PatientHistories')
                        ->groupBy('PatientId');
                })
                ->latest('CreatedDate'))
            ->columns([
                TextColumn::make('patient')
                    ->label('Name')
                    ->searchable()
                    ->formatStateUsing(fn ($state): string => $state->FirstName.' '.$state->LastName)
                    ->sortable(),
                TextColumn::make('diseases.Name')
                    ->label('Clinical Details')
                    ->badge()
                    ->limitList(2)
                    ->separator(',')
                    ->description(fn (PatientHistory $record) => 'Symptoms: '.$record->symptoms->pluck('Name')->take(3)->implode(', '))
                    ->wrap(),
                // Stack 2: Prescriptions
                TextColumn::make('prescriptions_count')
                    ->counts('prescriptions')
                    ->label('Meds')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),
                // Stack 3: Financials (Consultation + Medicine Fees)
                TextColumn::make('ConsultationFee')
                    ->label('Fees')
                    ->numeric()
                    ->weight(FontWeight::Bold)
                    ->prefix('Consult: ')
                    ->description(fn (PatientHistory $record) => 'Meds: '.number_format($record->MedicinesFee))
                    ->sortable(),
                // Stack 4: Timeline (Created + Next Date)
                TextColumn::make('CreatedDate')
                    ->label('Timeline')
                    ->dateTime('M d, Y h:i A')
                    ->sortable()
                    ->description(fn (PatientHistory $record) => $record->NextAppointmentDate
                        ? 'Next: '.$record->NextAppointmentDate->format('M d, Y')
                        : 'No follow-up'
                    ),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                Action::make('view')
                    ->button()
                    ->color('info')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Prescription Details')
                    ->modalContent(fn (PatientHistory $record): HtmlString => new HtmlString(
                        view('order.view-modal', [
                            'history' => $record,
                            'patient' => $record->patient,
                            'clinic' => $record->patient->clinic,
                        ])->render()
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->slideOver()
                    ->modalWidth('5xl'),
                Action::make('print')
                    ->button()
                    ->color('gray')
                    ->icon('heroicon-o-printer')
                    ->url(fn (PatientHistory $record): string => route('order.print', $record))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
