<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PatientHistoryMedicine extends Pivot
{
    use AuditFields, HasUuids;

    protected $table = 'PatientHistoryMedicines';

    public function patientHistory(): BelongsTo
    {
        return $this->belongsTo(PatientHistory::class, 'PatientHistoryId');
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class, 'MedicineId');
    }

    protected function casts(): array
    {
        return [
            'IsDeleted' => 'boolean',
        ];
    }
}
