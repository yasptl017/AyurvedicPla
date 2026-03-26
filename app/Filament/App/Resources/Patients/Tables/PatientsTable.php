<?php

namespace App\Filament\App\Resources\Patients\Tables;

use App\Models\AwaitingPatientEntry;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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
                    ->description(fn($record) => $record->Email) // Stacks Email here
                    ->searchable(['FirstName', 'LastName', 'MiddleName', 'Email'])
                    ->sortable('FirstName')
                    ->weight('bold'),

                // 2. BADGE: Gender and Group (Visual flair)
                TextColumn::make('Gender')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Male' => 'info',
                        'Female' => 'danger', // or 'pink' if you have custom colors
                        default => 'gray',
                    })
                    ->description(fn($record) => $record->AgeGroup)
                    ->sortable(),

                // 3. STACKED: Date of Birth with exact age below
                TextColumn::make('BirthDate')
                    ->label('Date of Birth')
                    ->date('d/m/Y')
                    ->sortable(),

                // 4. Weight (Simple numeric with unit suffix)
                TextColumn::make('Weight')
                    ->numeric()
                    ->suffix(' kg')
                    ->sortable(),

                // 5. STACKED: Phone with Address below
                TextColumn::make('MobileNo')
                    ->label('Contact')
                    ->icon('heroicon-m-phone')
                    ->copyable() // Nice UX feature to copy number
                    ->description(fn($record) => Str::limit($record->Address, 30))
                    ->searchable(['MobileNo', 'Address']),
            ])
            ->defaultSort('BirthDate', 'desc')->filters([
                TrashedFilter::make(),
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
                ->withCount('patientHistories')
                ->withCount([
                    'awaitingEntries as active_awaiting_count' => fn (Builder $awaitingQuery) => $awaitingQuery
                        ->where('ClinicId', Filament::getTenant()?->Id)
                        ->whereDate('QueueDate', now()->timezone(config('app.timezone'))->toDateString()),
                ])
                ->orderByDesc('CreatedDate'))
            ->recordClasses(fn ($record) => (($record->active_awaiting_count ?? 0) > 0 || $record->patient_histories_count === 0) ? 'no-history-row' : null)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
