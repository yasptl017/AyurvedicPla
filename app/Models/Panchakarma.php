<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Panchakarma extends Model
{
    protected $primaryKey = "Id";
    protected $table = 'Panchakarmas';

    public function getCreatedAtColumn(): string
    {
        return 'CreatedDate';
    }

    public function getUpdatedAtColumn(): string
    {
        return 'ModifiedDate';
    }

    protected function casts(): array
    {
        return [
            'IsDeleted' => 'boolean',
        ];
    }
}
