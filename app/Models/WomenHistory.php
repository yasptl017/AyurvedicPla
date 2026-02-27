<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WomenHistory extends Model
{
    use HasUuids;

    public $timestamps = false;
    protected $primaryKey = 'Id';
    protected $table = 'WomenHistories';

    public function patientHistory(): BelongsTo
    {
        return $this->belongsTo(PatientHistory::class, 'PatientHistoryId');
    }
}
