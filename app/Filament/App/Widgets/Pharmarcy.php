<?php

namespace App\Filament\App\Widgets;

use App\Models\PatientHistory;
use App\Support\ExcelExport;
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
use Symfony\Component\HttpFoundation\StreamedResponse;

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
                        ->from('patienthistories')
                        ->groupBy('PatientId');
                })
                ->latest('CreatedDate'))
            ->columns([
                TextColumn::make('patient')
                    ->label('Name')
                    ->searchable()
                    ->formatStateUsing(fn ($state): string => trim(implode(' ', array_filter([
                        $state->FirstName,
                        $state->MiddleName,
                        $state->LastName,
                    ]))))
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
                    ->dateTime('d/m/Y h:i A', config('app.timezone'))
                    ->sortable()
                    ->description(fn (PatientHistory $record) => $record->NextAppointmentDate
                        ? 'Next: '.$record->NextAppointmentDate->timezone(config('app.timezone'))->format('d/m/Y')
                        : 'No follow-up'
                    ),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('exportExcel')
                    ->label('Export to Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn (): StreamedResponse => $this->exportToExcel()),
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
                Action::make('printMeds')
                    ->button()
                    ->label('Print Meds')
                    ->color('gray')
                    ->icon('heroicon-o-printer')
                    ->url(fn (PatientHistory $record): string => route('order.print-meds', $record))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    protected function exportToExcel(): StreamedResponse
    {
        $timezone = config('app.timezone');

        $rows = $this->getFilteredSortedTableQuery()
            ->with(['patient', 'diseases', 'symptoms', 'prescriptions.medicine'])
            ->get()
            ->map(fn (PatientHistory $record): array => [
                $record->CreatedDate?->timezone($timezone)->format('d/m/Y h:i A'),
                trim(implode(' ', array_filter([$record->patient?->FirstName, $record->patient?->MiddleName, $record->patient?->LastName]))),
                $record->patient?->MobileNo,
                $record->diseases->pluck('Name')->filter()->implode(', '),
                $record->symptoms->pluck('Name')->filter()->implode(', '),
                $record->prescriptions->map(fn ($prescription): ?string => $prescription->medicine?->Name)->filter()->implode(', '),
                $record->prescriptions->count(),
                (float) $record->ConsultationFee,
                (float) $record->MedicinesFee,
                (float) $record->ConsultationFee + (float) $record->MedicinesFee,
                $record->NextAppointmentDate?->timezone($timezone)->format('d/m/Y'),
            ]);

        return ExcelExport::download('pharmacy', [
            'Visit Date', 'Patient Name', 'Mobile No', 'Diseases', 'Symptoms', 'Medicines',
            'Medicines Count', 'Consultation Fee', 'Medicines Fee', 'Total', 'Next Appointment',
        ], $rows);
    }
}
