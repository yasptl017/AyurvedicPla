<?php

namespace App\Filament\App\Widgets;

use App\Models\PatientHistory;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class MonthlyPatientsChart extends ChartWidget
{
    protected ?string $heading = 'Monthly Patients';

    protected int|string|array $columnSpan = 'full';

    public ?string $filter = null;

    protected function getFilters(): ?array
    {
        $months = [];

        for ($i = 0; $i < 12; $i++) {
            $month = now()->subMonths($i);
            $months[$month->format('Y-m')] = $month->format('F Y');
        }

        return $months;
    }

    protected function getData(): array
    {
        $clinicId = Filament::getTenant()?->Id;

        $selectedMonth = $this->filter
            ? Carbon::createFromFormat('Y-m', $this->filter)
            : now();

        $daysInMonth = $selectedMonth->daysInMonth;

        $patientsPerDay = PatientHistory::query()
            ->whereHas('patient', fn ($query) => $query->where('ClinicId', $clinicId))
            ->whereYear('CreatedDate', $selectedMonth->year)
            ->whereMonth('CreatedDate', $selectedMonth->month)
            ->get()
            ->groupBy(fn ($history) => $history->CreatedDate->format('j'))
            ->map(fn ($histories) => $histories->pluck('PatientId')->unique()->count());

        $labels = [];
        $data = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $labels[] = $day;
            $data[] = $patientsPerDay->get((string) $day, 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Patients',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
