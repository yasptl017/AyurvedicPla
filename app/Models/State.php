<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use AuditFields;

    protected $table = 'States';

    protected function casts(): array
    {
        return [
            'IsDeleted' => 'boolean',
        ];
    }
}
