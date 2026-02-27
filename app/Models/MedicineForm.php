<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Model;

class MedicineForm extends Model
{
    use AuditFields;

    protected $table = 'MedicineForms';

    protected function casts(): array
    {
        return [
        ];
    }
}
