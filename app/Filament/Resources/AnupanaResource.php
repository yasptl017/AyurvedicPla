<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnupanaResource\Pages;
use App\Models\Anupana;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
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

class AnupanaResource extends Resource
{
    protected static ?string $model = Anupana::class;

    protected static ?string $slug = "anupanas";

    protected static string|null|UnitEnum $navigationGroup = "Medicine Management";

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'Name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedServerStack;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make("Name"),
            TextInput::make("NameGujarati"),
            Textarea::make("Description")->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("Name")->searchable(),
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
            "index" => Pages\ListAnupanas::route("/"),
            "create" => Pages\CreateAnupana::route("/create"),
            "edit" => Pages\EditAnupana::route("/{record}/edit"),
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
        return ["Name", "NameGujarati"];
    }
}
