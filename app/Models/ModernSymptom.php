<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ModernSymptom extends Model
{
    use AuditFields, HasUuids;

    protected $table = 'ModernSymptoms';
    protected $attributes = [
        'IsPrivate' => false
    ];

    protected function casts(): array
    {
        return [
            'IsPrivate' => 'boolean',
            'IsDeleted' => 'boolean',
        ];
    }
}
