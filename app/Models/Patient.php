<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Patient extends Model
{
    use AuditFields, HasUuids;

    protected $table = 'Patients';

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class, 'ClinicId');
    }


    public function custom_tab_data()
    {
        return $this->hasOne(self::class, 'Id', 'Id');
    }

    public function patientHistories(): HasMany
    {
        return $this->hasMany(PatientHistory::class, 'PatientId');
    }

    public function patientFiles(): HasMany
    {
        return $this->hasMany(PatientFile::class, 'Patient_id');
    }

    public function captures(): HasMany
    {
        return $this->HasMany(ImageCapture::class);
    }

    public function prakruti(): HasOne
    {
        return $this->hasOne(PatientPrakruti::class, 'PatientId');
    }

    public function sketches(): HasMany
    {
        return $this->hasMany(Sketch::class, 'Patient_id');
    }

    protected function casts(): array
    {
        return [
            'IsDeleted' => 'boolean',
        ];
    }
}
