<?php

namespace App\Filament\App\Pages;

use App\Filament\App\Widgets\Pharmarcy;
use BackedEnum;
use Filament\Pages\Page;

class Pharmacy extends Page
{
    protected static string|BackedEnum|null $navigationIcon =
        'hugeicons-medicine-02';
    protected static ?int $navigationSort = 5;
    protected string $view = 'filament.app.pages.pharmacy';

    public function getHeaderWidgets(): array
    {
        return [Pharmarcy::class];
    }
}
