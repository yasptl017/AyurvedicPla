<?php

namespace App\Filament\App\Resources\Patients\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;

class CustomTabDataRelationManager extends RelationManager
{
    protected static string $relationship = 'custom_tab_data';
    protected static ?string $label = "Sketch";
    protected string $view = 'sketch';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            SignaturePad::make('Sketch')->columnSpanFull()
        ]);
    }

}
