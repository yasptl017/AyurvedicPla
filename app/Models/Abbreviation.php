<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Model;

class Abbreviation extends Model
{
    use AuditFields;

    protected $table = 'abbreviations';

    protected function casts(): array
    {
        return [
        ];
    }
}
