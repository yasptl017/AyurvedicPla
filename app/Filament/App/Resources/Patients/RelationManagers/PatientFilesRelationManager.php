<?php

namespace App\Filament\App\Resources\Patients\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;


class PatientFilesRelationManager extends RelationManager
{
    protected static string $relationship = 'patientFiles';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FileUpload::make('File')
                    ->preserveFilenames(true)
                    ->disk('local')
                    ->directory('patient-files/' . Filament::getTenant()->Id)
                    ->visibility('private')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            // 1. Grid Layout
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            // 2. Style the "Card" container (White bg, shadow, rounded)
            ->recordClasses(['bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm rounded-xl flex flex-col justify-between h-full hover:shadow-md transition-all'])
            ->columns([
                Stack::make([
                    // --- A. IMAGE PREVIEW ---
                    ImageColumn::make('File')
                        ->disk('local')
                        ->height('160px') // Fixed height for consistency
                        ->width('100%')
                        ->extraImgAttributes(['class' => 'object-cover w-full rounded-t-xl'])
                        ->visible(fn($record) => $this->isImage($record?->File)),

                    // --- B. NON-IMAGE PLACEHOLDER ---
                    // Creates a gray box with an icon if not an image
                    TextColumn::make('id') // Dummy binding
                    ->formatStateUsing(fn() => '') // No text, just icon
                    ->icon('heroicon-o-document-text')
                        ->iconColor('gray')
                        ->size(TextSize::Large)
                        ->extraAttributes([
                            'class' => 'flex justify-center items-center w-full h-[160px] bg-gray-50 dark:bg-gray-800 rounded-t-xl',
                            'style' => 'font-size: 10rem;'
                        ])
                        ->visible(fn($record) => !$this->isImage($record?->File)),

                    // --- C. FILENAME & DETAILS ---
                    // This extracts the actual name from the path
                    TextColumn::make('File_name')
                        ->state(fn($record) => $record->File) // Get the path
                        ->formatStateUsing(fn($state) => basename($state)) // Extract 'report.pdf'
                        ->weight(FontWeight::Bold)
                        ->color('gray')
                        ->limit(25) // Prevent long names from breaking layout
                        ->tooltip(fn($state) => basename($state))
                        ->extraAttributes(['class' => 'px-4 pt-4 pb-2']), // Padding inside card
                ])->space(0), // Remove default stack spacing so we control it
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make(),
            ])
            // 3. ACTIONS (Download / Delete)
            ->recordActions([
                // Custom Download Action Button
                Action::make('download')
                    ->label('View')
                    ->icon(Heroicon::Eye)
                    ->color('primary')
                    ->url(fn($record) => route('patient.files.download', ['record' => $record->id]))
                    ->openUrlInNewTab()
                    ->button() // Makes it look like a button
                    ->size('xs'),

                DeleteAction::make()
                    ->iconButton()
                    ->color('danger'),
            ], position: RecordActionsPosition::AfterColumns); // Places actions at bottom right of card
    }

    // Helper to check file extension
    protected function isImage($file): bool
    {
        if (!$file) return false;
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'tiff']);
    }
}
