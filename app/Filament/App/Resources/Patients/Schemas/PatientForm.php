<?php

namespace App\Filament\App\Resources\Patients\Schemas;

use App\Models\MainPrakrutiBodyPartOrFood;
use emmanpbarrameda\FilamentTakePictureField\Forms\Components\TakePicture;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

class PatientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('FirstName')->required(),
                TextInput::make('MiddleName'),
                TextInput::make('LastName')->required(),
                DateTimePicker::make('BirthDate'),
                TextInput::make('Weight')
                    ->numeric(),
                TextInput::make('MobileNo'),
                TextInput::make('Email')->email(),
                TextInput::make('OtherIdNumber'),
                Radio::make('Gender')->options([
                    'male' => 'Male', 'female' => 'Female', 'others' => 'Others',
                ])->columns(3),
                TextArea::make('Address')->columnSpanFull(),
                TakePicture::make('Image')
                    ->label('Patient Image')
                    ->disk('public')
                    ->visibility('public')
                    ->showCameraSelector()
                    ->aspect('16:9')
                    ->imageQuality(80)
                    ->shouldDeleteOnEdit(false),

                Textarea::make('complain_of'),

                Select::make('consultation_fees_type')
                    ->label('Consultation Fees')
                    ->options([
                        'Full' => 'Full',
                        'Half' => 'Half',
                        'Free' => 'Free',
                    ])
                    ->default('Full')
                    ->required(),

                Section::make('Prakruti Analysis')
                    ->columnSpanFull()
                    ->relationship('prakruti')
                    ->headerActions([
                        // THE MODAL ACTION
                        Action::make('openCalculator')
                            ->label('Run Calculator')
                            ->icon('heroicon-o-calculator')
                            ->modalHeading('Prakruti Assessment')
                            ->modalWidth(Width::MaxContent) // Wide modal for the radio buttons
                            ->form(static::getPrakrutiCalculationSchema()) // The Radio buttons
                            ->action(function (array $data, Set $set) {
                                static::calculateAndSetResults($data, $set);
                            }),
                    ])
                    ->schema([
                        Grid::make(7)
                            ->schema([
                                TextInput::make('VatCount')->label('Vata Count')->readOnly()->default(0),
                                TextInput::make('PitCount')->label('Pitta Count')->readOnly()->default(0),
                                TextInput::make('KufCount')->label('Kapha Count')->readOnly()->default(0),

                                TextInput::make('VatPercentage')->label('Vata %')->readOnly()->suffix('%')->default(0),
                                TextInput::make('PitPercentage')->label('Pitta %')->readOnly()->suffix('%')->default(0),
                                TextInput::make('KufPercentage')->label('Kapha %')->readOnly()->suffix('%')->default(0),

                                TextInput::make('Total')->readOnly()->default(0),
                            ]),
                    ]),

            ])->columns(3);
    }

    public static function getPrakrutiCalculationSchema(): array
    {
        $fieldsets = MainPrakrutiBodyPartOrFood::query()
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
                                    return [$item->Symptoms => "({$item->mainPrakruti->Name}) $item->Symptoms"];
                                })->toArray()
                            ),
                    ])
                    ->columnSpan(1);
            })
            ->values()
            ->toArray();

        // 2. Wrap all those fieldsets in a Grid
        return [
            Grid::make([
                'default' => 1,
                'md' => 2,
            ])
                ->schema($fieldsets),
        ];
    }

    /**
     * This processes the modal data and updates the main form fields
     */
    protected static function calculateAndSetResults(array $data, Set $set): void
    {
        // 1. Get selected symptoms from modal data
        $selectedSymptoms = array_filter(array_values($data));

        if (empty($selectedSymptoms)) {
            return;
        }

        // 2. Query Doshas
        $prakrutiMap = MainPrakrutiBodyPartOrFood::query()
            ->whereIn('Symptoms', $selectedSymptoms)
            ->with('mainPrakruti')
            ->get();

        $counts = ['Vata' => 0, 'Pitta' => 0, 'Kapha' => 0];

        foreach ($prakrutiMap as $item) {
            $name = $item->mainPrakruti->Name ?? '';
            if (stripos($name, 'VAT') !== false) {
                $counts['Vata']++;
            } elseif (stripos($name, 'PIT') !== false) {
                $counts['Pitta']++;
            } elseif (stripos($name, 'KAF') !== false || stripos($name, 'Kapha') !== false) {
                $counts['Kapha']++;
            }
        }

        $total = array_sum($counts);

        // 3. SET the values on the main form
        // Since this Action is inside the 'prakruti' relationship section,
        // $set points directly to the related model's fields.
        $set('prakruti.VatCount', $counts['Vata']);
        $set('prakruti.PitCount', $counts['Pitta']);
        $set('prakruti.KufCount', $counts['Kapha']);
        $set('prakruti.Total', $total);

        if ($total > 0) {
            $set('prakruti.VatPercentage', (int) round(($counts['Vata'] / $total) * 100));
            $set('prakruti.PitPercentage', (int) round(($counts['Pitta'] / $total) * 100));
            $set('prakruti.KufPercentage', (int) round(($counts['Kapha'] / $total) * 100));
        }
    }
}
