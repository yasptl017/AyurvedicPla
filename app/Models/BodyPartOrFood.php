<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Model;

class BodyPartOrFood extends Model
{
    use AuditFields;

    protected $table = 'BodyPartOrFoods';

    protected function casts(): array
    {
        return [
        ];
    }
}
