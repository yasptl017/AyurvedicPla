<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\ClinicsRelationManager;
use App\Models\User;
use BackedEnum;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $slug = "users";

    protected static string|null|UnitEnum $navigationGroup = "User Management";

    protected static ?int $navigationSort = 1;

    protected static bool $isScopedToTenant = false;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make("Email")->required(),

            TextInput::make("PhoneNumber"),

            TextInput::make("FirstName"),

            TextInput::make("LastName"),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("Email"),

                TextColumn::make("PhoneNumber"),

                TextColumn::make("FirstName"),

                TextColumn::make("LastName"),

                TextColumn::make("IsAdmin"),
            ])
            ->filters([
                //
            ])
            ->recordActions([EditAction::make(), DeleteAction::make(), AttachAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [ClinicsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListUsers::route("/"),
            "create" => Pages\CreateUser::route("/create"),
            "edit" => Pages\EditUser::route("/{record}/edit"),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ["name"];
    }
}
