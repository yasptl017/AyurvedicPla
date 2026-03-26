<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\AuditFields;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AwaitingPatientEntry extends Model
{
    use AuditFields;

    protected $table = 'awaiting_patient_entries';

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'PatientId');
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class, 'ClinicId');
    }

    public static function addForClinicAndPatient(int|string|null $clinicId, int|string $patientId, CarbonInterface|string|null $date = null): ?self
    {
        if (! $clinicId) {
            return null;
        }

        return static::query()->firstOrCreate([
            'ClinicId' => $clinicId,
            'PatientId' => $patientId,
            'QueueDate' => static::normalizeDate($date),
        ]);
    }

    public static function removeForClinicAndPatientOnDate(int|string|null $clinicId, int|string $patientId, CarbonInterface|string|null $date = null): void
    {
        if (! $clinicId) {
            return;
        }

        static::query()
            ->where('ClinicId', $clinicId)
            ->where('PatientId', $patientId)
            ->whereDate('QueueDate', static::normalizeDate($date))
            ->get()
            ->each
            ->delete();
    }

    protected static function normalizeDate(CarbonInterface|string|null $date = null): string
    {
        if ($date instanceof CarbonInterface) {
            return $date->timezone(config('app.timezone'))->toDateString();
        }

        if (filled($date)) {
            return Carbon::parse($date)->timezone(config('app.timezone'))->toDateString();
        }

        return now()->timezone(config('app.timezone'))->toDateString();
    }

    protected function casts(): array
    {
        return [
            'QueueDate' => 'date',
            'IsDeleted' => 'boolean',
        ];
    }
}
