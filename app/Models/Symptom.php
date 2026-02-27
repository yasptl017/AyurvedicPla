<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Symptom extends Model
{
    use AuditFields;

    protected $table = 'Symptoms';

    public function diseases(): BelongsToMany
    {
        return $this->belongsToMany(Disease::class, 'DiseaseSymptoms', 'SymptomId', 'DiseaseId')->withPivot('IsMain');
    }

    public function diseaseTypes(): BelongsToMany
    {
        return $this->belongsToMany(DiseaseType::class, 'DiseaseTypeSymptoms', 'SymptomId', 'DiseaseTypeId')->withPivot('IsMain');
    }

    protected function casts(): array
    {
        return [
            'IsSpecial' => 'boolean',
        ];
    }
}
