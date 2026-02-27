<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiseaseInvestigationsResource\Pages;
use App\Models\DiseaseInvestigation;
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

class DiseaseInvestigationsResource extends Resource
{
    protected static ?string $model = DiseaseInvestigation::class;

    protected static ?string $slug = "disease-investigations";

    protected static string|null|UnitEnum $navigationGroup = "Disease Management";

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = "Name";

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make("DiseaseId")
                ->relationship("disease", "Name")
                ->required(),

            TextInput::make("Name"),
            RichEditor::make("Description"),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("Name"),

                TextColumn::make("disease.Name"),
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
            "index" => Pages\ListDiseaseInvestigations::route("/"),
            "create" => Pages\CreateDiseaseInvestigations::route("/create"),
            "edit" => Pages\EditDiseaseInvestigations::route("/{record}/edit"),
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
