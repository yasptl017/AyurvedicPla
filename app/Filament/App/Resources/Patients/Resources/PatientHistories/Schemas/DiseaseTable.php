<?php

namespace App\Filament\App\Resources\Patients\Resources\PatientHistories\Schemas;

use Filament\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DiseaseTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Name')
                    ->searchable(),
            ])->paginated([5, 10, 25])->defaultPaginationPageOption(10)->headerActions([
                CreateAction::make('Create Disease')
            ]);

    }


}
