<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Model;

class TimeOfAdministration extends Model
{
    use AuditFields;

    protected $table = 'TimeOfAdministrations';

    protected function casts(): array
    {
        return [
            'IsDeleted' => 'boolean',
            'IsShow' => 'boolean',
        ];
    }
}
