<?php

namespace App\Filament\App\Pages;

use App\Enums\UserRole;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use UnitEnum;

class Staffs extends Page implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    protected static string|null|BackedEnum $navigationIcon = Heroicon::OutlinedUser;
    protected static string|null|UnitEnum $navigationGroup = 'Management';
    protected static ?int $navigationSort = 1;
    protected string $view = 'filament.app.pages.staffs';

    public function table(Table $table): Table
    {

        return $table
            ->query(User::query()->with('clinics')
                ->whereHas('clinics', fn($query) => $query->where('DoctorId', Filament::getTenant()?->Id)))
            ->columns([
                TextColumn::make('FirstName'),
                TextColumn::make('LastName'),
                TextColumn::make('email'),
                TextColumn::make('Password'),
            ])
            ->headerActions([
                Action::make('Add Staff')->button()->schema([
                    TextInput::make('FirstName')->required(),
                    TextInput::make('LastName')->required(),
                    TextInput::make('Email')->required()->email(),
                    TextInput::make('Phone')->required(),
                    TextInput::make('Password')->required(),
                    Select::make('role')->options(UserRole::class)->required(),
                ])->action(function (array $data) {
                    $user = User::query()->create([
                        'FirstName' => $data['FirstName'],
                        'LastName' => $data['LastName'],
                        'Email' => $data['Email'],
                        'PhoneNumber' => $data['Phone'],
                        'Password' => $data['Password'],
                        'IsAdmin' => false
                    ]);
                    $user->clinics()->attach(Filament::getTenant()?->Id, ['role' => $data['role']]);
                })
            ]);


    }


}
