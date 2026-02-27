<?php

namespace App\Filament\Resources\PrakrutiResource\Pages;

use App\Filament\Resources\PrakrutiResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPrakruti extends EditRecord
{
    protected static string $resource = PrakrutiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
