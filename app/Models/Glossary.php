<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Model;

class Glossary extends Model
{
    use AuditFields;

    protected $table = 'Glossaries';

    protected function casts(): array
    {
        return [
        ];
    }
}
