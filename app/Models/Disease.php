<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Disease extends Model
{
    use AuditFields;

    protected $table = 'diseases';

    public function symptoms(): BelongsToMany
    {
        return $this->belongsToMany(Symptom::class, 'diseasesymptoms', 'DiseaseId', 'SymptomId')->withPivot('IsMain');
    }

    public function laboratoryReports(): BelongsToMany
    {
        return $this->belongsToMany(LaboratoryReport::class, 'diseaselaboratoryreports', 'DiseaseId', 'LaboratoryReportId');
    }

    protected function casts(): array
    {
        return [
        ];
    }
}
