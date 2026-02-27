<?php

namespace App\Filament\App\Resources\Patients\Resources\PatientHistories;

use App\Filament\App\Resources\Patients\PatientResource;
use App\Filament\App\Resources\Patients\Resources\PatientHistories\Pages\CreatePatientHistory;
use App\Filament\App\Resources\Patients\Resources\PatientHistories\Pages\EditPatientHistory;
use App\Filament\App\Resources\Patients\Resources\PatientHistories\Schemas\PatientHistoryForm;
use App\Filament\App\Resources\Patients\Resources\PatientHistories\Tables\PatientHistoriesTable;
use App\Models\PatientHistory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PatientHistoryResource extends Resource
{
    protected static bool $isScopedToTenant = false;
    protected static ?string $model = PatientHistory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $parentResource = PatientResource::class;


    public static function form(Schema $schema): Schema
    {
        return PatientHistoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PatientHistoriesTable::configure($table);
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'create' => CreatePatientHistory::route('/create'),
            'edit' => EditPatientHistory::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
