<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PatientHistoryDisease extends Pivot
{

    use HasUuids, AuditFields;

    protected $primaryKey = 'Id';
    protected $table = 'PatientHistoryDiseases';

    public function disease(): BelongsTo
    {
        return $this->belongsTo(Disease::class, 'DiseaseId');
    }

    public function diseaseType(): BelongsTo
    {
        return $this->belongsTo(DiseaseType::class, 'DiseaseTypeId');
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
