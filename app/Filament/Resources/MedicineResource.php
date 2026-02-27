<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicineResource\Pages;
use App\Models\Anupana;
use App\Models\Disease;
use App\Models\DiseaseType;
use App\Models\DiseaseTypeMedicine;
use App\Models\Medicine;
use App\Models\MedicineForm;
use App\Models\TimeOfAdministration;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class MedicineResource extends Resource
{
    protected static ?string $model = DiseaseTypeMedicine::class;

    protected static ?string $slug = "medicines";

    protected static string|null|UnitEnum $navigationGroup = "Management";

    protected static ?string $navigationLabel = 'Medicines';

    protected static ?int $navigationSort = 2;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;


    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('DiseaseId')
                ->label('Disease')
                ->options(fn () => Disease::query()->pluck('Name', 'Id'))
                ->searchable()
                ->preload()
                ->live()
                ->required()
                ->dehydrated(false),

            Select::make('DiseaseTypeId')
                ->label('Disease Type')
                ->options(function (Get $get) {
                    $diseaseId = $get('DiseaseId');

                    if (! $diseaseId) {
                        return [];
                    }

                    return DiseaseType::query()
                        ->where('DiseaseId', $diseaseId)
                        ->pluck('Name', 'Id');
                })
                ->searchable()
                ->preload()
                ->required(),

            Radio::make('medicine_mode')
                ->label('Medicine')
                ->options([
                    'existing' => 'Select Existing',
                    'new' => 'Create New',
                ])
                ->default('existing')
                ->live()
                ->inline()
                ->columnSpanFull(),

            Select::make('MedicineId')
                ->label('Medicine')
                ->options(fn () => Medicine::query()->pluck('Name', 'Id'))
                ->searchable()
                ->preload()
                ->required(fn (Get $get) => $get('medicine_mode') !== 'new')
                ->visible(fn (Get $get) => $get('medicine_mode') !== 'new')
                ->live()
                ->afterStateUpdated(function ($state, Get $get, \Filament\Schemas\Components\Utilities\Set $set) {
                    if (! $state) {
                        $set('MedicineFormId', null);
                        $set('CompanyName', null);
                        return;
                    }

                    $medicine = Medicine::query()
                        ->select(['Id', 'MedicineFormId', 'CompanyName'])
                        ->find($state);

                    $set('MedicineFormId', $medicine?->MedicineFormId);
                    $set('CompanyName', $medicine?->CompanyName);
                }),

            TextInput::make('new_medicine_name')
                ->label('Medicine Name')
                ->visible(fn (Get $get) => $get('medicine_mode') === 'new')
                ->required(fn (Get $get) => $get('medicine_mode') === 'new'),

            Select::make('new_medicine_form_id')
                ->label('Medicine Form')
                ->options(fn () => MedicineForm::query()->pluck('Name', 'Id'))
                ->searchable()
                ->preload()
                ->visible(fn (Get $get) => $get('medicine_mode') === 'new')
                ->required(fn (Get $get) => $get('medicine_mode') === 'new'),

            TextInput::make('new_medicine_company')
                ->label('Company Name')
                ->visible(fn (Get $get) => $get('medicine_mode') === 'new'),

            Select::make('MedicineFormId')
                ->label('Medicine Form')
                ->options(fn () => MedicineForm::query()->pluck('Name', 'Id'))
                ->searchable()
                ->preload()
                ->required(fn (Get $get) => $get('medicine_mode') !== 'new')
                ->visible(fn (Get $get) => $get('medicine_mode') !== 'new'),

            TextInput::make('Dose')
                ->required(),

            Select::make('TimeOfAdministrationId')
                ->label('Time Of Administration')
                ->options(fn () => TimeOfAdministration::query()->pluck('Name', 'Id'))
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('Duration')
                ->label('Quantity')
                ->required(),

            Select::make('AnupanaId')
                ->label('Anupana')
                ->options(fn () => Anupana::query()->pluck('Name', 'Id'))
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('CompanyName')
                ->label('Company Name')
                ->required(fn (Get $get) => $get('medicine_mode') !== 'new')
                ->visible(fn (Get $get) => $get('medicine_mode') !== 'new'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                if (! Filament::getTenant()) {
                    return $query;
                }

                return $query->whereHas('medicine', fn (Builder $medicine) => $medicine->where('ClinicId', Filament::getTenant()->Id));
            })
            ->columns([
                TextColumn::make('diseaseType.disease.Name')
                    ->label('Disease')
                    ->searchable(),
                TextColumn::make('diseaseType.Name')
                    ->label('Disease Type')
                    ->searchable(),
                TextColumn::make('medicine.Name')
                    ->label('Medicine')
                    ->searchable(),
                TextColumn::make('medicine.medicineForm.Name')
                    ->label('Medicine Form'),
                TextColumn::make('Dose'),
                TextColumn::make('timeOfAdministration.Name')
                    ->label('Time Of Administration'),
                TextColumn::make('Duration')
                    ->label('Quantity'),
                TextColumn::make('anupana.Name')
                    ->label('Anupana'),
                TextColumn::make('medicine.CompanyName')
                    ->label('Company Name')
                    ->searchable(),
            ])
            ->filters([TrashedFilter::make()])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListMedicines::route("/"),
            "create" => Pages\CreateMedicine::route("/create"),
            "edit" => Pages\EditMedicine::route("/{record}/edit"),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
