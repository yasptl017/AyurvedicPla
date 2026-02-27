<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Model;

class MainPrakruti extends Model
{
    use AuditFields;

    protected $table = 'MainPrakrutis';

    protected function casts(): array
    {
        return [
            'IsDeleted' => 'boolean',
        ];
    }
}
