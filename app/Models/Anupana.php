<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Model;

class Anupana extends Model
{
    use AuditFields;

    protected $table = 'Anupanas';

    protected function casts(): array
    {
        return [
            'IsShow' => 'boolean',
        ];
    }
}
