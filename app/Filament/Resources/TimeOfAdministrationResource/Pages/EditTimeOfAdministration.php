<?php

namespace App\Filament\Resources\TimeOfAdministrationResource\Pages;

use App\Filament\Resources\TimeOfAdministrationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditTimeOfAdministration extends EditRecord
{
    protected static string $resource = TimeOfAdministrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
