<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientPrakruti extends Model
{
    use AuditFields, HasUuids;

    protected $table = 'patientprakrutis';

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'PatientId');
    }

    protected function casts(): array
    {
        return [
            'IsDeleted' => 'boolean',
            'Responses' => 'array',
        ];
    }
}
