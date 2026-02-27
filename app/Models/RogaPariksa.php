<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RogaPariksa extends Model
{
    use AuditFields, HasUuids;

    protected $table = 'RogaPariksas';

    protected $attributes = [
        'DeletedBy' => '',
        'IsDeleted' => false
    ];

    protected function casts(): array
    {
        return [
            'Vat' => 'boolean',
            'Pit' => 'boolean',
            'Kaf' => 'boolean',
            'Rasa' => 'boolean',
            'Rakta' => 'boolean',
            'Mansa' => 'boolean',
            'Meda' => 'boolean',
            'Asthi' => 'boolean',
            'Majja' => 'boolean',
            'Shukra' => 'boolean',
            'Stanya' => 'boolean',
            'Raja' => 'boolean',
            'Kandara' => 'boolean',
            'Sira' => 'boolean',
            'Dhamani' => 'boolean',
            'Twacha' => 'boolean',
            'Snau' => 'boolean',
            'Poorisha' => 'boolean',
            'Mootra' => 'boolean',
            'Sweda' => 'boolean',
            'Kapha' => 'boolean',
            'Pitta' => 'boolean',
            'Khamala' => 'boolean',
            'Kesha' => 'boolean',
            'Nakha' => 'boolean',
            'Akshisneha' => 'boolean',
            'Loma' => 'boolean',
            'Shmashru' => 'boolean',
            'Sanaga' => 'boolean',
            'Vimargagamana' => 'boolean',
            'Atipravrutti' => 'boolean',
            'Sira_granthi' => 'boolean',
            'Koshtha' => 'boolean',
            'Shakha' => 'boolean',
            'Marma' => 'boolean',
            'IsDeleted' => 'boolean',
        ];
    }
}
