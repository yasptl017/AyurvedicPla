<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiseaseInvestigation extends Model
{
    use AuditFields;

    protected $table = 'DiseaseInvestigations';

    public function disease(): BelongsTo
    {
        return $this->belongsTo(Disease::class, 'DiseaseId');
    }
}
