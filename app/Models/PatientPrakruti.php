<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientPrakruti extends Model
{
    use AuditFields, HasUuids;

    protected $table = 'PatientPrakrutis';

    public function patient(): belongsto
    {
        return $this->belongsto(patient::class, 'patientid');
    }

    protected function casts(): array
    {
        return [
            'isdeleted' => 'boolean',
        ];
    }
}
