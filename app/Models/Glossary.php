<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Model;

class Glossary extends Model
{
    use AuditFields;

    protected $table = 'glossaries';

    protected function casts(): array
    {
        return [
        ];
    }
}
