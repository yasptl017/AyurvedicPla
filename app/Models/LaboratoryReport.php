<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LaboratoryReport extends Model
{
    use AuditFields;

    protected $table = 'LaboratoryReports';

    public function diseases(): BelongsToMany
    {
        return $this->belongsToMany(Disease::class, 'DiseaseLaboratoryReports', 'LaboratoryReportId', 'DiseaseId');
    }

    protected function casts(): array
    {
        return [
        ];
    }
}
