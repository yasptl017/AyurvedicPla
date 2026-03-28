<?php

namespace App\Filament\App\Pages;

use App\Models\Clinic;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class ClinicProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|null|UnitEnum $navigationGroup = 'Management';

    protected static ?string $navigationLabel = 'Clinic Profile';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.app.pages.clinic-profile';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->getClinic()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model($this->getClinic())
            ->statePath('data')
            ->components([
                Section::make('Clinic Details')
                    ->schema([
                        TextInput::make('ClinicName')
                            ->label('Clinic Name')
                            ->required(),
                        TextInput::make('DoctorName')
                            ->label('Doctor Name')
                            ->required(),
                        Textarea::make('Address')
                            ->label('Address')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        FileUpload::make('Logo')
                            ->label('Logo (Optional)')
                            ->image()
                            ->disk('public')
                            ->directory(fn (): string => 'clinics/' . $this->getClinic()->Id)
                            ->visibility('public')
                            ->columnSpanFull(),
                        TextInput::make('Email')
                            ->label('Email (Optional)')
                            ->email(),
                        TextInput::make('RegistrationNo')
                            ->label('Registration No.')
                            ->required(),
                        TextInput::make('MobileNo')
                            ->label('Mobile Number')
                            ->required(),
                        TextInput::make('MobileNo2')
                            ->label('Mobile Number 2 (Optional)'),
                        TextInput::make('ClinicTiming')
                            ->label('Clinic Timing (Optional)')
                            ->columnSpanFull(),
                        Textarea::make('WarningField1')
                            ->label('Warning Field 1 (Optional)')
                            ->rows(2)
                            ->columnSpanFull(),
                        Textarea::make('WarningField2')
                            ->label('Warning Field 2 (Optional)')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->getClinic()->update($data);

        Notification::make()
            ->success()
            ->title('Clinic profile saved')
            ->send();
    }

    protected function getClinic(): Clinic
    {
        $clinic = Filament::getTenant();

        abort_unless($clinic instanceof Clinic, 404);

        return $clinic;
    }
}
