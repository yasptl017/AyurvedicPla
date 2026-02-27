<?php

namespace App\Filament\Resources\AnupanaResource\Pages;

use App\Filament\Resources\AnupanaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAnupana extends CreateRecord
{
    protected static string $resource = AnupanaResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
