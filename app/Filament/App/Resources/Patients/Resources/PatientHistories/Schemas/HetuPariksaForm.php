<?php

namespace App\Filament\App\Resources\Patients\Resources\PatientHistories\Schemas;

use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Group;

class HetuPariksaForm
{
    public static function configure()
    {

        return
            Group::make()
                ->relationship('hetuPariksa')
                ->schema([
                    ViewField::make('Responses')
                        ->view('hetu-form')
                        ->afterStateHydrated(function (ViewField $component, $state): void {
                            if (empty($state)) {
                                $component->state((object) []);
                            }
                        })
                        ->hiddenLabel(),
                ]);
    }
}
