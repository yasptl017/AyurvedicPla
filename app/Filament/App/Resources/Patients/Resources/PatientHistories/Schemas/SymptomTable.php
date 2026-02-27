<?php

namespace App\Filament\App\Resources\Patients\Resources\PatientHistories\Schemas;

use App\Models\Symptom;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SymptomTable
{

    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) use ($table) {
                $diagnoses = $table->getArguments()['diseases'];
                return $query->when($diagnoses, function ($query) use ($diagnoses) {
                    $query->whereHas('diseases', function ($query) use ($diagnoses) {
                        $query->whereIn('DiseaseId', $diagnoses);
                    });
                });
            })
            ->emptyStateHeading('No Symptoms Found')
            ->emptyStateDescription('Select A Disease To Proceed')
            ->columns([
                TextColumn::make('Name')
                    ->description(fn(Symptom $symptom) => $symptom->NameGujarati)
                    ->searchable()
            ])->defaultPaginationPageOption(10);

    }


}
