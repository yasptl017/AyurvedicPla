<?php

namespace App\Filament\App\Resources\Patients\RelationManagers;

use App\Models\MainPrakrutiBodyPartOrFood;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Radio;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PrakrutiRelationManager extends RelationManager
{
    protected static string $relationship = 'prakruti';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(function () {
                return MainPrakrutiBodyPartOrFood::query()
                    ->with(['bodyPartOrFood', 'mainPrakruti'])
                    ->get()
                    ->groupBy('bodyPartOrFood.Name')
                    ->map(function ($items, $groupName) {
                        return Fieldset::make($groupName)
                            ->schema([
                                Radio::make($groupName)
                                    ->hiddenLabel()
                                    ->options(
                                        $items->mapWithKeys(function ($item) {
                                            $label = "({$item->mainPrakruti->Name}) $item->Symptoms";
                                            return [$item->Symptoms => $label];
                                        })->toArray()
                                    )
                            ]);

                    })
                    ->values()
                    ->toArray();
            });
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Id')
            ->columns([
                TextColumn::make('VatCount')->label('Vata')->numeric()->default(0),
                TextColumn::make('PitCount')->label('Pitta')->numeric()->default(0),
                TextColumn::make('KufCount')->label('Kapha')->numeric()->default(0),

                TextColumn::make('VatPercentage')->label('Vata %')->suffix('%'),
                TextColumn::make('PitPercentage')->label('Pitta %')->suffix('%'),
                TextColumn::make('KufPercentage')->label('Kapha %')->suffix('%'),

                TextColumn::make('Total'),
                TextColumn::make('CreatedDate')->date(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->modalWidth(width: Width::MaxContent)
                    ->mutateDataUsing(function (array $data) {
                        return $this->processPrakrutiData($data);
                    })->createAnother(false)
                    ->hidden(fn($livewire) => $livewire->getOwnerRecord()->prakruti !== null),

            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth(width: Width::MaxContent)
                    ->mutateDataUsing(function (array $data) {
                        return $this->processPrakrutiData($data);
                    }),
            ]);
    }


    protected function processPrakrutiData(array $data): array
    {
        // 1. Extract the selected symptoms (the values from the radio buttons)
        // We filter out any null values just in case
        $selectedSymptoms = array_filter(array_values($data));

        // 2. Query the DB to find which Dosha these symptoms belong to
        $prakrutiMap = MainPrakrutiBodyPartOrFood::query()
            ->whereIn('Symptoms', $selectedSymptoms)
            ->with('mainPrakruti')
            ->get();

        // 3. Initialize Counters
        $counts = [
            'Vata' => 0,
            'Pitta' => 0,
            'Kapha' => 0,
        ];

        // 4. Count the Doshas
        foreach ($prakrutiMap as $item) {
            $name = $item->mainPrakruti->Name ?? '';

            // Logic to match your Dosha names.
            // Adjust 'Vat', 'Pit', 'Kuf' strings if your DB names are different.
            if (stripos($name, 'VAT') !== false) {
                $counts['Vata']++;
            } elseif (stripos($name, 'PIT') !== false) {
                $counts['Pitta']++;
            } elseif (stripos($name, 'KAF') !== false || stripos($name, 'Kapha') !== false) {
                $counts['Kapha']++;
            }
        }

        // 5. Calculate Totals and Percentages
        $total = array_sum($counts);
        $vatPct = $total > 0 ? ($counts['Vata'] / $total) * 100 : 0;
        $pitPct = $total > 0 ? ($counts['Pitta'] / $total) * 100 : 0;
        $kufPct = $total > 0 ? ($counts['Kapha'] / $total) * 100 : 0;

        return [
            'VatCount' => $counts['Vata'],
            'PitCount' => $counts['Pitta'],
            'KufCount' => $counts['Kapha'],
            'VatPercentage' => (int)round($vatPct),
            'PitPercentage' => (int)round($pitPct),
            'KufPercentage' => (int)round($kufPct),
            'Total' => $total,

            'IsDeleted' => 0,
        ];
    }


}
