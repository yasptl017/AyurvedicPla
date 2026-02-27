<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DiseaseType extends Model
{
    use AuditFields;

    protected $table = 'DiseaseTypes';

    public function disease(): BelongsTo
    {
        return $this->belongsTo(Disease::class, 'DiseaseId');
    }

    public function symptoms(): BelongsToMany
    {
        return $this->belongsToMany(Symptom::class, 'DiseaseTypeSymptoms', 'DiseaseTypeId', 'SymptomId')->withPivot('IsMain');
    }

    public function medicines(): BelongsToMany
    {
        return $this->belongsToMany(
            Medicine::class, 'DiseaseTypeMedicines', 'DiseaseTypeId', 'MedicineId')
            ->where(fn($query) => $query->where('Medicines.IsSpecial', false)->orWhere('Medicines.CreatedBy', auth()->user()->Id))->withPivot([
                'Dose',
                
            ]);

    }

    protected function casts(): array
    {
        return [
            'IsSpecial' => 'boolean',
        ];
    }
}
