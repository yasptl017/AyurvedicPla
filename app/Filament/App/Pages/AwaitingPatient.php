<?php

namespace App\Filament\App\Pages;

use BackedEnum;
use Filament\Pages\Page;

class AwaitingPatient extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Awaiting Patients';
    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.app.pages.awaiting-patient';

    public function getHeaderWidgets(): array
    {
        return [\App\Filament\App\Widgets\AwaitingPatient::class];
    }
}
