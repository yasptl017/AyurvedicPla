<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientFile extends Model
{
    protected $fillable = [
        'Patient_id',
        'patient_history_id',
        'File',
    ];

    protected static function booted(): void
    {
        static::creating(function (PatientFile $model) {
            if (! $model->Patient_id && $model->patient_history_id) {
                $model->Patient_id = $model->patientHistory?->PatientId;
            }
        });
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'Patient_id');
    }

    public function patientHistory(): BelongsTo
    {
        return $this->belongsTo(PatientHistory::class, 'patient_history_id');
    }
}
