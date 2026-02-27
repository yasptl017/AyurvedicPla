<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PatientHistoryPanchakarma extends Pivot
{
    use HasUuids, AuditFields;

    protected $table = 'PatientHistoryPanchakarmas';

    protected $attributes = [
        'IsDeleted' => false,
    ];

    public function panchakarma(): BelongsTo
    {
        return $this->belongsTo(Panchakarma::class, 'PanchakarmaId');
    }
}
