<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\Patients\PatientResource;
use App\Models\AwaitingPatientEntry;
use App\Models\Patient;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class AwaitingPatient extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => AwaitingPatientEntry::query()
                ->where('ClinicId', Filament::getTenant()?->Id)
                ->whereDate('QueueDate', now()->timezone(config('app.timezone'))->toDateString())
                ->with([
                    'patient' => fn ($query) => $query->withCount('patientHistories'),
                ])
                ->latest('CreatedDate'))
            ->recordUrl(fn (AwaitingPatientEntry $record): string => PatientResource::getUrl('edit', ['record' => $record->PatientId]))
            ->columns([
                TextColumn::make('patient')
                    ->color('primary')
                    ->label('Name')
                    ->formatStateUsing(fn (AwaitingPatientEntry $record) => trim(implode(' ', array_filter([
                        $record->patient?->FirstName,
                        $record->patient?->MiddleName,
                        $record->patient?->LastName,
                    ]))))
                    ->description(fn (AwaitingPatientEntry $record) => $record->patient?->Email)
                    ->searchable(['patient.FirstName', 'patient.LastName', 'patient.MiddleName', 'patient.Email'])
                    ->sortable('PatientId')
                    ->weight('bold'),

                TextColumn::make('patient.Gender')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Male' => 'info',
                        'Female' => 'danger',
                        default => 'gray',
                    })
                    ->description(fn (AwaitingPatientEntry $record) => $record->patient?->AgeGroup)
                    ->sortable(),

                TextColumn::make('patient.BirthDate')
                    ->label('Date of Birth')
                    ->date('d/m/Y')
                    ->description(fn (AwaitingPatientEntry $record) => "{$record->patient?->AgeYear} yrs, {$record->patient?->AgeMonth} mos")
                    ->sortable(),

                TextColumn::make('patient.Weight')
                    ->numeric()
                    ->suffix(' kg')
                    ->sortable(),

                TextColumn::make('patient.MobileNo')
                    ->label('Contact')
                    ->icon('heroicon-m-phone')
                    ->copyable()
                    ->description(fn (AwaitingPatientEntry $record) => Str::limit($record->patient?->Address, 30))
                    ->searchable(['patient.MobileNo', 'patient.Address']),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('addPatient')
                    ->label('Add Patient')
                    ->icon('heroicon-o-plus')
                    ->schema([
                        Select::make('PatientId')
                            ->label('Patient')
                            ->required()
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search): array {
                                return Patient::query()
                                    ->where('ClinicId', Filament::getTenant()?->Id)
                                    ->where(function (Builder $query) use ($search): void {
                                        $query
                                            ->where('FirstName', 'like', "%{$search}%")
                                            ->orWhere('MiddleName', 'like', "%{$search}%")
                                            ->orWhere('LastName', 'like', "%{$search}%")
                                            ->orWhere('MobileNo', 'like', "%{$search}%");
                                    })
                                    ->orderBy('FirstName')
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(fn (Patient $patient) => [
                                        $patient->Id => trim(implode(' ', array_filter([
                                            $patient->FirstName,
                                            $patient->MiddleName,
                                            $patient->LastName,
                                        ]))) . ($patient->MobileNo ? " ({$patient->MobileNo})" : ''),
                                    ])
                                    ->all();
                            })
                            ->getOptionLabelUsing(function ($value): ?string {
                                $patient = Patient::query()->find($value);

                                if (! $patient) {
                                    return null;
                                }

                                return trim(implode(' ', array_filter([
                                    $patient->FirstName,
                                    $patient->MiddleName,
                                    $patient->LastName,
                                ])));
                            }),
                    ])
                    ->action(function (array $data): void {
                        AwaitingPatientEntry::addForClinicAndPatient(
                            clinicId: Filament::getTenant()?->Id,
                            patientId: $data['PatientId'],
                        );

                        Notification::make()
                            ->success()
                            ->title('Patient added to today\'s waiting list')
                            ->send();
                    }),
            ])
            ->recordActions([
                Action::make('remove')
                    ->label('Remove')
                    ->icon('heroicon-o-x-mark')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(function (AwaitingPatientEntry $record): void {
                        $record->delete();

                        Notification::make()
                            ->success()
                            ->title('Patient removed from today\'s waiting list')
                            ->send();
                    }),
            ])
            ->recordClasses(fn (AwaitingPatientEntry $record) => ($record->patient?->patient_histories_count ?? 0) === 0
                ? 'waiting-new-row'
                : 'waiting-return-row')
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
