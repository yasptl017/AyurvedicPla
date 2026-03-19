<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AstavidhyaPariksha extends Model
{
    use AuditFields, HasUuids;

    public $timestamps = false;

    protected $table = 'astavidhyaparikshas';

    protected $attributes = [
        'DeletedBy' => '0000-0000-0000-0000',
        'IsDeleted' => false,
    ];

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
