<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Relations\Pivot;

class DoctorUser extends Pivot
{
    protected $table = 'DoctorUsers';
    protected $primaryKey = 'Id';

    protected function casts(): array
    {
        return [
            'role' => UserRole::class
        ];
    }
}
