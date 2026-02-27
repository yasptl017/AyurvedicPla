<?php

namespace App\Filament\Resources\AnupanaResource\Pages;

use App\Filament\Resources\AnupanaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAnupanas extends ListRecords
{
    protected static string $resource = AnupanaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
