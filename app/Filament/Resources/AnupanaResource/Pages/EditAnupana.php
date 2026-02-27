<?php

namespace App\Filament\Resources\AnupanaResource\Pages;

use App\Filament\Resources\AnupanaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditAnupana extends EditRecord
{
    protected static string $resource = AnupanaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
