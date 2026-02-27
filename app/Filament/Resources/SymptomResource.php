<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SymptomResource\Pages;
use App\Models\Symptom;
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

class SymptomResource extends Resource
{
    protected static ?string $model = Symptom::class;

    protected static ?string $slug = "symptoms";

    protected static string|null|UnitEnum $navigationGroup = "Disease Management";

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'Name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFaceFrown;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make("Name"),
            TextInput::make("NameEnglish"),
            TextInput::make("NameGujarati"),
            RichEditor::make("Description")->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("Name")->searchable(),

                TextColumn::make("NameEnglish")->searchable(),

                TextColumn::make("NameGujarati")->searchable(),
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
            "index" => Pages\ListSymptoms::route("/"),
            "create" => Pages\CreateSymptom::route("/create"),
            "edit" => Pages\EditSymptom::route("/{record}/edit"),
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
        return ["Name", "NameEnglish", "NameGujarati"];
    }
}
