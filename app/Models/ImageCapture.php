<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImageCapture extends Model
{
    protected $fillable = [
        'patient_id',
        'patient_history_id',
        'capture',
    ];

    protected static function booted(): void
    {
        static::creating(function (ImageCapture $model) {
            if (! $model->patient_id && $model->patient_history_id) {
                $model->patient_id = $model->patientHistory?->PatientId;
            }
        });
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function patientHistory(): BelongsTo
    {
        return $this->belongsTo(PatientHistory::class, 'patient_history_id');
    }
}
