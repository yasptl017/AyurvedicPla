<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\Patients\PatientResource;
use App\Models\PatientHistory;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class ClientAppointments extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => PatientHistory::query()
                    ->whereHas('clinic', fn ($query) => $query->where('ClinicId', Filament::getTenant()->Id))
                    ->with(['patient', 'diseases'])
                    ->whereNotNull('NextAppointmentDate')
            )
            ->columns([
                TextColumn::make('patient')
                    ->label('Patient Name')
                    ->formatStateUsing(fn ($record) => trim(implode(' ', array_filter([
                        $record->patient->FirstName,
                        $record->patient->MiddleName,
                        $record->patient->LastName,
                    ]))))
                    ->searchable(['patient.FirstName', 'patient.LastName', 'patient.MiddleName', 'patient.Email'])
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('patient.MobileNo')
                    ->label('Mobile')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('patient.Address')
                    ->label('Address')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('diagnosis')
                    ->label('Diagnosis')
                    ->state(fn (PatientHistory $record) => $record->diseases
                        ->pluck('Name')
                        ->filter()
                        ->unique()
                        ->join(', '))
                    ->wrap(),

                TextColumn::make('NextAppointmentDate')
                    ->label('Next Appointment')
                    ->dateTime('d/m/Y h:i A', config('app.timezone'))
                    ->sortable()
                    ->badge(),

                TextColumn::make('CreatedDate')
                    ->label('Created At')
                    ->badge()
                    ->dateTime('d/m/Y h:i A', config('app.timezone'))
                    ->sortable(),
            ])
            ->emptyStateHeading('No Appointments')
            ->filters([
                SelectFilter::make('Range')
                    ->options([
                        'today' => 'Today\'s Appointments',
                        'upcoming' => 'Upcoming Appointments',
                        'past' => 'Past Appointments',
                        'all' => 'All Appointments',
                    ])
                    ->default('upcoming')
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value']) || $data['value'] === 'all') {
                            return $query;
                        }

                        return match ($data['value']) {
                            'today' => $query->where('NextAppointmentDate', '>=', now()->startOfDay())
                                ->where('NextAppointmentDate', '<=', now()->endOfDay()),
                            'upcoming' => $query->where('NextAppointmentDate', '>=', now()),
                            'past' => $query->where('NextAppointmentDate', '<', now()),
                            default => $query,
                        };
                    }),
            ])
            ->recordUrl(fn ($record) => PatientResource::getUrl('edit', [
                'record' => $record->PatientId,
                'tenant' => Filament::getTenant(),
                'relation' => '0',
            ]))
            ->recordActions([
                Action::make('visitPatient')
                    ->label('View Patient')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => PatientResource::getUrl('edit', [
                        'record' => $record->PatientId,
                        'tenant' => Filament::getTenant(),
                        'relation' => '0',
                    ])),
            ])
            ->defaultSort('NextAppointmentDate', 'asc');
    }
}
