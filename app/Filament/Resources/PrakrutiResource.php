<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrakrutiResource\Pages;
use App\Models\MainPrakrutiBodyPartOrFood;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class PrakrutiResource extends Resource
{
    protected static ?string $model = MainPrakrutiBodyPartOrFood::class;
    protected static ?string $slug = "prakrutis";
    protected static string|null|UnitEnum $navigationGroup = "Body & Constitution";
    protected static ?int $navigationSort = 1;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFaceSmile;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make("Symptoms")]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("MainPrakruti.Name"),
                TextColumn::make("Symptoms"),
            ])
            ->striped()
            ->defaultGroup(
                Group::make("BodyPartOrFood.Name")
                    ->label("Characteristic")
                    ->collapsible(),
            )
            ->filters([])
            ->recordActions([EditAction::make()])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListPrakrutis::route("/"),
            //            'create' => Pages\CreatePrakruti::route('/create'),
            "edit" => Pages\EditPrakruti::route("/{record}/edit"),
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
