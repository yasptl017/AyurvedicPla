<?php

namespace App\Filament\Resources\PrakrutiResource\Pages;

use App\Filament\Resources\PrakrutiResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrakruti extends CreateRecord
{
    protected static string $resource = PrakrutiResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
