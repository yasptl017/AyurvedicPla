<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiseaseResource\RelationManagers\SymptomsRelationManager;
use App\Filament\Resources\DiseaseTypeResource\Pages;
use App\Filament\Resources\DiseaseTypeResource\RelationManagers\MedicinesRelationManager;
use App\Models\DiseaseType;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class DiseaseTypeResource extends Resource
{
    protected static ?string $model = DiseaseType::class;

    protected static ?string $slug = "disease-types";

    protected static string|null|UnitEnum $navigationGroup = "Disease Management";

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = "Name";

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make("DiseaseId")
                ->relationship("disease", "Name")
                ->searchable()
                ->required(),

            TextInput::make("Name"),

            RichEditor::make("Description")->columnSpanFull(),

            Textarea::make("Do"),

            Textarea::make("Dont"),

            TextInput::make("OrderNumber")->integer(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("Name")->searchable(),

                TextColumn::make("disease.Name")->searchable(),
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

    public static function getRelations(): array
    {
        return [
            SymptomsRelationManager::class,
            MedicinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListDiseaseTypes::route("/"),
            "create" => Pages\CreateDiseaseType::route("/create"),
            "edit" => Pages\EditDiseaseType::route("/{record}/edit"),
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
        return ["Name"];
    }
}
