<?php

namespace App\Filament\Resources\AbbreviationResource\Pages;

use App\Filament\Resources\AbbreviationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAbbreviation extends CreateRecord
{
    protected static string $resource = AbbreviationResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
