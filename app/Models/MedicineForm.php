<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Model;

class MedicineForm extends Model
{
    use AuditFields;

    protected $table = 'medicineforms';

    protected function casts(): array
    {
        return [
        ];
    }
}
