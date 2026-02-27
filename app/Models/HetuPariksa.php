<?php

namespace App\Models;

use App\Traits\AuditFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HetuPariksa extends Model
{
    use AuditFields, HasUuids;

    public $timestamps = false;

    protected $table = 'HetuPariksas';

    protected $attributes = [
        'DeletedBy' => '0000-0000-0000-0000',
        'IsDeleted' => false,
    ];

    public function patientHistory(): BelongsTo
    {
        return $this->belongsTo(PatientHistory::class, 'PatientHistoryId');
    }

    protected function casts(): array
    {
        return [
            'Question1_Vat' => 'boolean',
            'Question1_Pit' => 'boolean',
            'Question1_Kuf' => 'boolean',
            'Question2_Vat' => 'boolean',
            'Question2_Pit' => 'boolean',
            'Question2_Kuf' => 'boolean',
            'Question3_Tobaco' => 'boolean',
            'Question3_Masalo' => 'boolean',
            'Question3_Cigrate' => 'boolean',
            'Question3_Alcohol' => 'boolean',
            'Question3_Vat' => 'boolean',
            'Question3_Pit' => 'boolean',
            'Question3_Kuf' => 'boolean',
            'Question4_Vat' => 'boolean',
            'Question4_Pit' => 'boolean',
            'Question4_Kuf' => 'boolean',
            'Question5_Vat' => 'boolean',
            'Question5_Pit' => 'boolean',
            'Question5_Kuf' => 'boolean',
            'Question6_Vat' => 'boolean',
            'Question6_Pit' => 'boolean',
            'Question6_Kuf' => 'boolean',
            'Question7_Vat' => 'boolean',
            'Question7_Pit' => 'boolean',
            'Question7_Kuf' => 'boolean',
            'Question8_Vat' => 'boolean',
            'Question8_Pit' => 'boolean',
            'Question8_Kuf' => 'boolean',
            'Question9_Vat' => 'boolean',
            'Question9_Pit' => 'boolean',
            'Question9_Kuf' => 'boolean',
            'Question10_Vat' => 'boolean',
            'Question10_Pit' => 'boolean',
            'Question10_Kuf' => 'boolean',
            'Question11_Vat' => 'boolean',
            'Question11_Pit' => 'boolean',
            'Question11_Kuf' => 'boolean',
            'Question12_Vat' => 'boolean',
            'Question12_Pit' => 'boolean',
            'Question12_Kuf' => 'boolean',
            'Question13_Vat' => 'boolean',
            'Question13_Pit' => 'boolean',
            'Question13_Kuf' => 'boolean',
            'Question14_Vat' => 'boolean',
            'Question14_Pit' => 'boolean',
            'Question14_Kuf' => 'boolean',
            'Question15_Vat' => 'boolean',
            'Question15_Pit' => 'boolean',
            'Question15_Kuf' => 'boolean',
            'Question16_Vat' => 'boolean',
            'Question16_Pit' => 'boolean',
            'Question16_Kuf' => 'boolean',
            'Question17_Vat' => 'boolean',
            'Question17_Pit' => 'boolean',
            'Question17_Kuf' => 'boolean',
            'Question18_Vat' => 'boolean',
            'Question18_Pit' => 'boolean',
            'Question18_Kuf' => 'boolean',
            'Question19_Vat' => 'boolean',
            'Question19_Pit' => 'boolean',
            'Question19_Kuf' => 'boolean',
            'Question20_Vat' => 'boolean',
            'Question20_Pit' => 'boolean',
            'Question20_Kuf' => 'boolean',
            'Question21_Vat' => 'boolean',
            'Question21_Pit' => 'boolean',
            'Question21_Kuf' => 'boolean',
            'Question22_Vat' => 'boolean',
            'Question22_Pit' => 'boolean',
            'Question22_Kuf' => 'boolean',
            'Question23_Vat' => 'boolean',
            'Question23_Pit' => 'boolean',
            'Question23_Kuf' => 'boolean',
            'Question24_Vat' => 'boolean',
            'Question24_Pit' => 'boolean',
            'Question24_Kuf' => 'boolean',
            'Question25_Vat' => 'boolean',
            'Question25_Pit' => 'boolean',
            'Question25_Kuf' => 'boolean',
            'Question26_Vat' => 'boolean',
            'Question26_Pit' => 'boolean',
            'Question26_Kuf' => 'boolean',
            'Question27_Vat' => 'boolean',
            'Question27_Pit' => 'boolean',
            'Question27_Kuf' => 'boolean',
            'Question28_Vat' => 'boolean',
            'Question28_Pit' => 'boolean',
            'Question28_Kuf' => 'boolean',
            'Question29_Vat' => 'boolean',
            'Question29_Pit' => 'boolean',
            'Question29_Kuf' => 'boolean',
            'Question30_Vat' => 'boolean',
            'Question30_Pit' => 'boolean',
            'Question30_Kuf' => 'boolean',
            'Question31_Vat' => 'boolean',
            'Question31_Pit' => 'boolean',
            'Question31_Kuf' => 'boolean',
            'Question32_Vat' => 'boolean',
            'Question32_Pit' => 'boolean',
            'Question32_Kuf' => 'boolean',
            'Question33_Vat' => 'boolean',
            'Question33_Pit' => 'boolean',
            'Question33_Kuf' => 'boolean',
            'Question34_Vat' => 'boolean',
            'Question34_Pit' => 'boolean',
            'Question34_Kuf' => 'boolean',
            'Question35_Vat' => 'boolean',
            'Question35_Pit' => 'boolean',
            'Question35_Kuf' => 'boolean',
            'IsDeleted' => 'boolean',
            'Responses' => 'array',
        ];
    }
}
