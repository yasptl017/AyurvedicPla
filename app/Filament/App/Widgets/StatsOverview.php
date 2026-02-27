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
        $patientCount = Patient::query()->where('ClinicId', Filament::getTenant()?->Id)->count();
        $revenue = PatientHistory::query()->whereHas('patient', function ($query) {
            $query->where('ClinicId', Filament::getTenant()?->Id);
        })->sum('ConsultationFee');
        $todayRevenue = PatientHistory::query()->whereHas('patient', function ($query) {
            $query->where('ClinicId', Filament::getTenant()?->Id);
        })->whereDate('CreatedDate', now())->sum('ConsultationFee');

        return [
            Stat::make('Patients', $patientCount),
            Stat::make('Total Revenue', $revenue),
            Stat::make('Today Revenue', $todayRevenue),
        ];
    }
}
