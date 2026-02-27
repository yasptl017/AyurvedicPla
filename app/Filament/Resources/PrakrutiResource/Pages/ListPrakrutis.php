<?php

namespace App\Filament\Resources\PrakrutiResource\Pages;

use App\Filament\Resources\PrakrutiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrakrutis extends ListRecords
{
    protected static string $resource = PrakrutiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
