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
            ->withPivot([
                'Id',
                'Dose',
                'TimeOfAdministrationId',
                'AnupanaId',
                'Duration',
                'IsSpecial',
                'CreatedBy',
                'ModifiedBy',
                'CreatedDate',
                'ModifiedDate',
                'DeletedBy',
                'IsDeleted',
                'OrderNumber',
            ]);

    }

    protected function casts(): array
    {
        return [
            'IsSpecial' => 'boolean',
        ];
    }
}
