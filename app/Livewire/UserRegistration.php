<?php

namespace App\Livewire;

use Filament\Auth\Pages\Register;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserRegistration extends Register
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('FirstName')
                    ->label('First Name')
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),
                TextInput::make('LastName')
                    ->label('Last Name')
                    ->required()
                    ->maxLength(255),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $data['IsAdmin'] = false;
        return $data;
    }


}
