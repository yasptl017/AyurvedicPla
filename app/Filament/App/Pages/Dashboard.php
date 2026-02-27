<?php

namespace App\Filament\App\Pages;

use App\Filament\App\Widgets\Calendar;
use App\Filament\App\Widgets\StatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?int $navigationSort = 1;

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            Calendar::class,
        ];
    }
}
