<?php

namespace App\Livewire;

use App\Models\City;
use App\Models\Clinic;
use App\Models\State;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Str;

class TenantRegistration extends RegisterTenant
{
    protected string|Width|null $maxWidth = Width::FourExtraLarge;

    public static function getLabel(): string
    {
        return "Register Clinic";
    }


    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('ClinicName')->label('Name')->required()->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('ClinicUrl', Str::slug($state))),
                TextInput::make("Email")->email()->required(),
                TextInput::make("MobileNo")->required(),
                TextInput::make("ClinicUrl")->required()->label("Subdomain"),
                Select::make('StateId')->label('State')
                    ->options(State::all()->pluck('Name', 'Id'))
                    ->live()
                    ->dehydrated(false)
                    ->afterStateUpdated(function (Set $set) {
                        $set('CityId', null);
                    }),

                Select::make('CityId')
                    ->label('city')
                    ->options(fn(Get $get): array => City::query()
                        ->where('cities.StateId', $get('StateId'))
                        ->pluck('Name', 'Id')
                        ->toArray()
                    )
                    ->disabled(fn(Get $get): bool => !filled($get('StateId')))
                    ->live(),
                Textarea::make("Address")->columnSpanFull()
                // ...
            ])->columns();
    }

    protected function handleRegistration(array $data): Clinic
    {
        $team = Clinic::query()->create($data);

        $team->users()->attach(auth()->user()->Id, [
            'role' => 'owner'
        ]);

        return $team;
    }
}
