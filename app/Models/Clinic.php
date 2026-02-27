<?php

namespace App\Models;

use App\Traits\AuditFields;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Clinic extends Model implements HasName
{
    use AuditFields;

    protected $table = 'Doctors';

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'DoctorUsers', 'DoctorId', 'UserId')->withPivot('role')->using(DoctorUser::class);
    }

    public function calendarAppointments(): HasMany
    {
        return $this->hasMany(CalendarAppointment::class, 'ClinicId');
    }

    public function getFilamentName(): string
    {
        return $this->ClinicName;
    }

    protected function casts(): array
    {
        return [
            'IsDeleted' => 'boolean',
        ];
    }
}
