<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Model;

class Abbreviation extends Model
{
    use AuditFields;

    protected $table = 'Abbreviations';

    protected function casts(): array
    {
        return [
        ];
    }
}
