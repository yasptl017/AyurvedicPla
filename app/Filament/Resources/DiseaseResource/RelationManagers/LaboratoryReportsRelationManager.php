<?php

namespace App\Filament\Resources\DiseaseResource\RelationManagers;

use App\Filament\Resources\LaboratoryReportResource;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LaboratoryReportsRelationManager extends RelationManager
{
    protected static string $relationship = 'laboratoryReports';

    protected static string|null|BackedEnum $icon = Heroicon::OutlinedBeaker;

    protected static ?string $relatedResource = LaboratoryReportResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
