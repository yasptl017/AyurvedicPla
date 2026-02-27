<?php

namespace App\Models;

use App\Traits\AuditFields;
use Guava\Calendar\Contracts\Eventable;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class PatientHistory extends Model implements Eventable
{
    use AuditFields, HasUuids;

    protected $table = 'PatientHistories';

    protected $attributes = [
        'ConsultationFee' => 0.0,
        'MedicinesFee' => 0.0,
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'PatientId');
    }

    public function diseases(): BelongsToMany
    {
        return $this->belongsToMany(Disease::class, 'PatientHistoryDiseases', 'PatientHistoryId', 'DiseaseId')->withPivot([
            'DiseaseId',
            'DiseaseTypeId',
        ])->using(PatientHistoryDisease::class);
    }

    public function symptoms(): BelongsToMany
    {
        return $this->belongsToMany(Symptom::class, 'PatientHistorySymptoms', 'PatientHistoryId', 'SymptomId')->using(PatientHistorySymptom::class);
    }

    public function womenHistory(): HasOne
    {
        return $this->hasOne(WomenHistory::class, 'PatientHistoryId');
    }

    public function vital(): HasOne
    {
        return $this->hasOne(PatientHistoryVital::class, 'PatientHistoryId');
    }

    public function clinic(): HasOneThrough
    {
        return $this->hasOneThrough(Clinic::class, Patient::class, 'Id', 'Id', 'PatientId', 'ClinicId');
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(PatientHistoryMedicine::class, 'PatientHistoryId', 'Id');

    }

    public function modernSymptoms(): BelongsToMany
    {
        return $this->belongsToMany(ModernSymptom::class, 'PatientHistoryModernSymptom', 'PatientHistoryId', 'SymptomId')->using(PatientHistoryModernSymptom::class);
    }

    public function hetuPariksa(): HasOne
    {
        return $this->hasOne(HetuPariksa::class, 'PatientHistoryId')->select(['Id', 'PatientHistoryId', 'Responses']);
    }

    public function panchakarmas(): BelongsToMany
    {
        return $this->belongsToMany(Panchakarma::class, 'PatientHistoryPanchakarmas', 'PatientHistoryId', 'PanchakarmaId')->using(PatientHistoryPanchakarma::class)->withPivot('Detail');

    }

    public function patientHistoryPanchakarmas(): HasMany
    {
        return $this->hasMany(PatientHistoryPanchakarma::class, 'PatientHistoryId');
    }

    public function patientFiles(): HasMany
    {
        return $this->hasMany(PatientFile::class, 'patient_history_id');
    }

    public function sketches(): HasMany
    {
        return $this->hasMany(Sketch::class, 'patient_history_id');
    }

    public function captures(): HasMany
    {
        return $this->hasMany(ImageCapture::class, 'patient_history_id');
    }

    public function patientRecords(): HasMany
    {
        return $this->hasMany(PatientRecord::class, 'patient_history_id');
    }

    public function rogaPariksa(): HasOne
    {
        return $this->hasOne(RogaPariksa::class);
    }

    public function toCalendarEvent(): CalendarEvent
    {
        return CalendarEvent::make($this)
            ->title($this->patient
                ? "{$this->patient->FirstName} {$this->patient->LastName}"
                : 'Unknown Patient')
            ->action('edit')
            ->allDay()
            ->start($this->NextAppointmentDate)
            ->end($this->NextAppointmentDate);
    }

    protected function casts(): array
    {
        return [
            'IsHetuPariksa' => 'boolean',
            'IsLaboratoryReport' => 'boolean',
            'IsPanchakarma' => 'boolean',
            'IsRogaPariksa' => 'boolean',
            'IsVital' => 'boolean',
            'IsWomenHistory' => 'boolean',
            'IsImages' => 'boolean',
            'NextAppointmentDate' => 'datetime',
        ];
    }
}
