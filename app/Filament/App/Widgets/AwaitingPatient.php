<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\Patients\PatientResource;
use App\Models\Patient;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Str;

class AwaitingPatient extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Patient::query()
                ->whereDoesntHave('patientHistories')
                ->orderByDesc('CreatedDate'))
            ->recordUrl(fn (Patient $record): string => PatientResource::getUrl('edit', ['record' => $record]))
            ->columns([
                TextColumn::make('FirstName')
                    ->color('primary')
                    ->label('Name')
                    ->formatStateUsing(fn ($record) => "{$record->FirstName} {$record->LastName}")
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

                // 3. STACKED: Date of Birth with exact age below
                TextColumn::make('BirthDate')
                    ->label('Date of Birth')
                    ->date() // Standard date format
                    ->description(fn ($record) => "{$record->AgeYear} yrs, {$record->AgeMonth} mos")
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
                    ->description(fn ($record) => Str::limit($record->Address, 30))
                    ->searchable(['MobileNo', 'Address']),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
