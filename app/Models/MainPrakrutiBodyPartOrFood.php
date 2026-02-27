<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MainPrakrutiBodyPartOrFood extends Model
{
    use AuditFields;

    protected $table = 'MainPrakrutiBodyPartOrFoods';

    public function bodyPartOrFood(): BelongsTo
    {
        return $this->belongsTo(BodyPartOrFood::class, 'BodyPartOrFoodId');
    }

    public function mainPrakruti(): BelongsTo
    {
        return $this->belongsTo(MainPrakruti::class, 'MainPrakrutiId');
    }

    protected function casts(): array
    {
        return [
            'IsDeleted' => 'boolean',
        ];
    }
}
