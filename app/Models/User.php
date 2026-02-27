<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUuids;

    public $timestamps = false;
    protected $table = 'AspNetUsers';
    protected $primaryKey = "Id";
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'Password',
        'RememberToken',
    ];

    public function getTenants(Panel $panel): Collection
    {
        return $this->clinics()->get();
    }

    public function clinics(): BelongsToMany
    {
        return $this->belongsToMany(Clinic::class, 'DoctorUsers', 'UserId', 'DoctorId')->withPivot('role')->using(DoctorUser::class);
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->clinics()->whereKey($tenant)->exists();
    }


    public function getAuthPassword()
    {
        return $this->Password;
    }

    /**
     * 3. (Optional) If your DB uses 'EmailAddress' instead of 'Email'
     * generally Laravel just uses whatever key you pass to Auth::attempt,
     * but for notifications, define this:
     */
    public function routeNotificationForMail($notification)
    {
        return $this->Email;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'EmailVerifiedAt' => 'datetime',
            'Password' => 'hashed',
        ];
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            get: fn($attributes) => $attributes['Email'] ?? null,
        );
    }

    /**
     * Map 'name' (Filament) -> 'Name' (Database)
     * Filament uses this for the top-right user menu
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->FirstName . ' ' . $this->LastName,
        );
    }
}
