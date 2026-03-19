<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Model;

class MainPrakruti extends Model
{
    use AuditFields;

    protected $table = 'mainprakrutis';

    protected function casts(): array
    {
        return [
            'IsDeleted' => 'boolean',
        ];
    }
}
