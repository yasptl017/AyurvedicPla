<?php

namespace App\Filament\App\Widgets;

use App\Models\Patient;
use App\Models\PatientHistory;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $clinicId = Filament::getTenant()?->Id;

        $todayPatientIds = PatientHistory::query()
            ->whereHas('patient', fn ($query) => $query->where('ClinicId', $clinicId))
            ->whereDate('CreatedDate', now())
            ->pluck('PatientId')
            ->unique();

        $todayNewPatients = 0;
        $todayOldPatients = 0;

        foreach ($todayPatientIds as $patientId) {
            $isNew = PatientHistory::query()->where('PatientId', $patientId)->count() === 1;

            if ($isNew) {
                $todayNewPatients++;
            } else {
                $todayOldPatients++;
            }
        }

        $todayMedicinesFee = PatientHistory::query()
            ->whereHas('patient', fn ($query) => $query->where('ClinicId', $clinicId))
            ->whereDate('CreatedDate', now())
            ->sum('MedicinesFee');

        $todayConsultationFee = PatientHistory::query()
            ->whereHas('patient', fn ($query) => $query->where('ClinicId', $clinicId))
            ->whereDate('CreatedDate', now())
            ->sum('ConsultationFee');

        $monthPatients = PatientHistory::query()
            ->whereHas('patient', fn ($query) => $query->where('ClinicId', $clinicId))
            ->whereYear('CreatedDate', now()->year)
            ->whereMonth('CreatedDate', now()->month)
            ->distinct('PatientId')
            ->count('PatientId');

        $monthMedicinesFee = PatientHistory::query()
            ->whereHas('patient', fn ($query) => $query->where('ClinicId', $clinicId))
            ->whereYear('CreatedDate', now()->year)
            ->whereMonth('CreatedDate', now()->month)
            ->sum('MedicinesFee');

        $monthConsultationFee = PatientHistory::query()
            ->whereHas('patient', fn ($query) => $query->where('ClinicId', $clinicId))
            ->whereYear('CreatedDate', now()->year)
            ->whereMonth('CreatedDate', now()->month)
            ->sum('ConsultationFee');

        $totalPatients = Patient::query()->where('ClinicId', $clinicId)->count();

        return [
            Stat::make('Today Patients', $todayPatientIds->count()),
            Stat::make('Today New Patients', $todayNewPatients),
            Stat::make('Today Old Patients', $todayOldPatients),
            Stat::make('Today Medicines Collection', number_format($todayMedicinesFee, 2)),
            Stat::make('Today Consultation Collection', number_format($todayConsultationFee, 2)),
            Stat::make('Current Month Patients', $monthPatients),
            Stat::make('Current Month Medicines Collection', number_format($monthMedicinesFee, 2)),
            Stat::make('Current Month Consultation Collection', number_format($monthConsultationFee, 2)),
            Stat::make('Total Patients', $totalPatients),
        ];
    }
}
