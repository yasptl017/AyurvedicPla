<?php

namespace App\Filament\App\Resources\Patients\RelationManagers;

use emmanpbarrameda\FilamentTakePictureField\Forms\Components\TakePicture;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;

class CapturesRelationManager extends RelationManager
{
    protected static string $relationship = 'captures';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                TakePicture::make('capture')
                    ->label('Capture')
                    ->disk('public')
                    ->visibility('public')
                    ->showCameraSelector()
                    ->aspect('16:9')
                    ->imageQuality(80)
                    ->shouldDeleteOnEdit(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('capture')
            ->contentGrid([

            ])
            ->columns([
                ImageColumn::make('capture')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
