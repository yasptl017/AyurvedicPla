<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Medicine extends Model
{
    use AuditFields;

    protected $table = 'Medicines';

    public function medicineForm(): BelongsTo
    {
        return $this->belongsTo(MedicineForm::class, 'MedicineFormId');
    }

    public function diseaseTypes(): BelongsToMany
    {

        return $this->belongsToMany(DiseaseType::class, 'DiseaseTypeMedicines', 'MedicineId', 'DiseaseTypeId');
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class, 'ClinicId');
    }

    protected function casts(): array
    {
        return [
            'IsPattern' => 'boolean',
            'IsSpecial' => 'boolean',
        ];
    }
}
