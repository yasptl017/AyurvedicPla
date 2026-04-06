<?php

namespace App\Filament\App\Resources\Patients\Tables;

use App\Models\AwaitingPatientEntry;
use App\Models\PatientHistory;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PatientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 1. STACKED: Full Name with Email below it
                TextColumn::make('FirstName')
                    ->label('Name')
                    ->formatStateUsing(fn ($record) => trim(implode(' ', array_filter([
                        $record->FirstName,
                        $record->MiddleName,
                        $record->LastName,
                    ]))))
                    ->description(fn ($record) => $record->Email) // Stacks Email here
                    ->searchable(['FirstName', 'LastName', 'MiddleName', 'Email'])
                    ->sortable('FirstName')
                    ->weight('bold'),

                // 2. BADGE: Gender and Group (Visual flair)
                TextColumn::make('Gender')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Male' => 'info',
                        'Female' => 'danger', // or 'pink' if you have custom colors
                        default => 'gray',
                    })
                    ->description(fn ($record) => $record->AgeGroup)
                    ->sortable(),

                TextColumn::make('latest_diagnosis')
                    ->label('Diagnosis')
                    ->wrap()
                    ->sortable(),

                // 4. STACKED: Phone with Address below
                TextColumn::make('MobileNo')
                    ->label('Contact')
                    ->icon('heroicon-m-phone')
                    ->copyable() // Nice UX feature to copy number
                    ->description(fn ($record) => Str::limit($record->Address, 30))
                    ->searchable(['MobileNo', 'Address']),

                TextColumn::make('first_visit_date')
                    ->label('First Visit')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('last_visit_date')
                    ->label('Last Visit')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('next_appointment_date')
                    ->label('Next Appointment')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('CreatedDate', 'desc')->filters([
                Filter::make('visit_date_range')
                    ->label('Visit Dates')
                    ->form([
                        Select::make('date_field')
                            ->label('Date Type')
                            ->options([
                                'first_visit_date' => 'First Visit',
                                'last_visit_date' => 'Last Visit',
                                'next_appointment_date' => 'Next Appointment',
                            ]),
                        DatePicker::make('from')->label('From')->displayFormat('d/m/Y')->native(false),
                        DatePicker::make('until')->label('To')->displayFormat('d/m/Y')->native(false),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => static::applyVisitDateRangeFilter($query, $data))
                    ->indicateUsing(function (array $data): array {
                        $labels = [
                            'first_visit_date' => 'First Visit',
                            'last_visit_date' => 'Last Visit',
                            'next_appointment_date' => 'Next Appointment',
                        ];

                        $indicators = [];

                        if ($data['date_field'] ?? null) {
                            $indicators[] = 'Date Type: '.$labels[$data['date_field']];
                        }

                        if ($data['from'] ?? null) {
                            $indicators[] = 'From: '.\Carbon\Carbon::parse($data['from'])->format('d/m/Y');
                        }

                        if ($data['until'] ?? null) {
                            $indicators[] = 'To: '.\Carbon\Carbon::parse($data['until'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),
            ])
            ->recordActions([
                Action::make('addToWaitingList')
                    ->label(fn ($record) => ($record->active_awaiting_count ?? 0) > 0 ? 'In Waiting' : 'Add to Waiting')
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->disabled(fn ($record) => ($record->active_awaiting_count ?? 0) > 0)
                    ->action(function ($record): void {
                        AwaitingPatientEntry::addForClinicAndPatient(
                            clinicId: Filament::getTenant()?->Id ?? $record->ClinicId,
                            patientId: $record->Id,
                        );

                        Notification::make()
                            ->success()
                            ->title('Patient added to today\'s waiting list')
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->addSelect([
                    'first_visit_date' => PatientHistory::query()
                        ->selectRaw('MIN(CreatedDate)')
                        ->whereColumn('PatientId', 'patients.Id'),
                    'last_visit_date' => PatientHistory::query()
                        ->selectRaw('MAX(CreatedDate)')
                        ->whereColumn('PatientId', 'patients.Id'),
                    'next_appointment_date' => PatientHistory::query()
                        ->select('NextAppointmentDate')
                        ->whereColumn('PatientId', 'patients.Id')
                        ->orderByDesc('CreatedDate')
                        ->limit(1),
                    'latest_diagnosis' => DB::table('patienthistories as ph')
                        ->selectRaw("GROUP_CONCAT(DISTINCT d.Name ORDER BY d.Name SEPARATOR ', ')")
                        ->join('patienthistorydiseases as phd', 'phd.PatientHistoryId', '=', 'ph.Id')
                        ->join('diseases as d', 'd.Id', '=', 'phd.DiseaseId')
                        ->whereNull('ph.DeletedDate')
                        ->whereRaw(
                            'ph.Id = (
                                select ph2.Id
                                from patienthistories as ph2
                                where ph2.PatientId = patients.Id
                                  and ph2.DeletedDate is null
                                order by ph2.CreatedDate desc
                                limit 1
                            )'
                        ),
                    'awaiting_created_date' => AwaitingPatientEntry::query()
                        ->select('CreatedDate')
                        ->whereColumn('PatientId', 'patients.Id')
                        ->where('ClinicId', Filament::getTenant()?->Id)
                        ->whereDate('QueueDate', now()->timezone(config('app.timezone'))->toDateString())
                        ->orderByDesc('CreatedDate')
                        ->limit(1),
                ])
                ->withCount('patientHistories')
                ->withCount([
                    'awaitingEntries as active_awaiting_count' => fn (Builder $awaitingQuery) => $awaitingQuery
                        ->where('ClinicId', Filament::getTenant()?->Id)
                        ->whereDate('QueueDate', now()->timezone(config('app.timezone'))->toDateString()),
                ])
                ->orderByRaw('CASE WHEN active_awaiting_count > 0 THEN 0 ELSE 1 END')
                ->orderByDesc('awaiting_created_date')
                ->orderByDesc('CreatedDate'))
            ->recordClasses(fn ($record) => match (true) {
                $record->patient_histories_count === 0 => 'waiting-new-row',
                ($record->active_awaiting_count ?? 0) > 0 && $record->patient_histories_count > 0 => 'waiting-return-row',
                default => null,
            })
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    protected static function applyVisitDateRangeFilter(Builder $query, array $data): Builder
    {
        $expression = static::getVisitDateExpression($data['date_field'] ?? null);

        if (! $expression) {
            return $query;
        }

        return $query
            ->when(
                $data['from'] ?? null,
                fn (Builder $builder, $date) => $builder->whereRaw("({$expression}) >= ?", [$date])
            )
            ->when(
                $data['until'] ?? null,
                fn (Builder $builder, $date) => $builder->whereRaw("({$expression}) <= ?", [$date])
            );
    }

    protected static function getVisitDateExpression(?string $field): ?string
    {
        return match ($field) {
            'first_visit_date' => 'select date(min(`CreatedDate`)) from `patienthistories` where `patienthistories`.`PatientId` = `patients`.`Id` and `patienthistories`.`DeletedDate` is null',
            'last_visit_date' => 'select date(max(`CreatedDate`)) from `patienthistories` where `patienthistories`.`PatientId` = `patients`.`Id` and `patienthistories`.`DeletedDate` is null',
            'next_appointment_date' => 'select date(`NextAppointmentDate`) from `patienthistories` where `patienthistories`.`PatientId` = `patients`.`Id` and `patienthistories`.`DeletedDate` is null order by `CreatedDate` desc limit 1',
            default => null,
        };
    }
}
