<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClinicsRelationManager extends RelationManager
{
    protected static string $relationship = 'clinics';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('ClinicName'),

                TextInput::make('ClinicUrl'),


                TextInput::make('MobileNo'),

                TextInput::make('Address'),

                TextInput::make('CityId')
                    ->integer(),

                TextInput::make('StateId')
                    ->integer(),


                TextInput::make('PrescriptionUrl'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([

                TextColumn::make('ClinicName'),

                TextColumn::make('ClinicUrl'),

                TextColumn::make('FirstName'),

                TextColumn::make('LastName'),

                TextColumn::make('Email'),

                TextColumn::make('MobileNo'),

                TextColumn::make('Address'),

                TextColumn::make('CityId'),

                TextColumn::make('StateId'),


                TextColumn::make('PrescriptionUrl'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
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
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}
