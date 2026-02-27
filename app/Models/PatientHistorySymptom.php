<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PatientHistorySymptom extends Pivot
{
    use HasUuids, AuditFields;

    protected $table = 'PatientHistorySymptoms';
    protected $attributes = [
        'IsDeleted' => false,
    ];

    public function symptom(): BelongsTo
    {
        return $this->belongsTo(Symptom::class, 'SymptomId');
    }

    public function patientHistory(): BelongsTo
    {
        return $this->belongsTo(PatientHistory::class, 'PatientHistoryId');
    }

    protected function casts(): array
    {
        return [
            'IsDeleted' => 'boolean',
        ];
    }
}
