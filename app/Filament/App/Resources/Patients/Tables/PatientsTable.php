<?php

namespace App\Filament\App\Resources\Patients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
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
                    ->formatStateUsing(fn($record) => "{$record->FirstName} {$record->LastName}")
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
                    ->date() // Standard date format
                    ->description(fn($record) => "{$record->AgeYear} yrs, {$record->AgeMonth} mos")
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
                EditAction::make(),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->withCount('patientHistories')->orderByDesc('CreatedDate'))
            ->recordClasses(fn($record) => $record->patient_histories_count === 0 ? 'no-history-row' : null)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
