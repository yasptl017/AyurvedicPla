<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiseaseTypeMedicine extends Model
{
    use AuditFields;

    protected $table = 'DiseaseTypeMedicines';

    protected $attributes = [
        'DeletedBy' => '',
        'IsDeleted' => false
    ];

    public function diseaseType(): BelongsTo
    {
        return $this->belongsTo(DiseaseType::class, 'DiseaseTypeId');
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class, 'MedicineId');
    }

    public function timeOfAdministration(): BelongsTo
    {
        return $this->belongsTo(TimeOfAdministration::class, 'TimeOfAdministrationId');
    }

    public function anupana(): BelongsTo
    {
        return $this->belongsTo(Anupana::class, 'AnupanaId');
    }

    protected function casts(): array
    {
        return [
            'IsDeleted' => 'boolean',
            'IsSpecial' => 'boolean',
            'IsLevel3' => 'boolean',
            'IsLevel1' => 'boolean',
            'IsLevel2' => 'boolean',
        ];
    }
}
