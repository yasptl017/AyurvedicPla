<?php

namespace App\Filament\App\Pages;

use App\Filament\App\Widgets\Calendar;
use App\Filament\App\Widgets\ClientAppointments;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Appointments extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Calendar;
    protected static ?int $navigationSort = 2;
    protected string $view = 'filament.app.pages.appointments';

    public function getHeaderWidgets(): array
    {
        return [
            ClientAppointments::class,
            Calendar::class
        ];
    }
}
