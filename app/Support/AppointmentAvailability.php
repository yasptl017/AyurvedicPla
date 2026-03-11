<?php

namespace App\Support;

use App\Models\CalendarAppointment;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class AppointmentAvailability
{
    /**
     * @var array<string, list<string>>
     */
    protected static array $unavailableDatesCache = [];

    /**
     * @return list<string>
     */
    public static function unavailableDatesForClinic(int|string|null $clinicId, int|string|null $ignoreAppointmentId = null): array
    {
        if (blank($clinicId)) {
            return [];
        }

        $cacheKey = implode(':', [(string) $clinicId, (string) ($ignoreAppointmentId ?? 'none')]);

        return self::$unavailableDatesCache[$cacheKey] ??= self::unavailableAppointmentQuery($clinicId, $ignoreAppointmentId)
            ->get(['StartDate', 'EndDate'])
            ->flatMap(fn (CalendarAppointment $appointment): array => self::expandDateRange($appointment->StartDate, $appointment->EndDate))
            ->unique()
            ->values()
            ->all();
    }

    public static function dateIsUnavailableForClinic(int|string|null $clinicId, CarbonInterface|string|null $date, int|string|null $ignoreAppointmentId = null): bool
    {
        if (blank($date)) {
            return false;
        }

        return in_array(
            self::normalizeToCarbon($date)->toDateString(),
            self::unavailableDatesForClinic($clinicId, $ignoreAppointmentId),
            true,
        );
    }

    public static function rangeTouchesUnavailableDateForClinic(
        int|string|null $clinicId,
        CarbonInterface|string|null $startDate,
        CarbonInterface|string|null $endDate = null,
        int|string|null $ignoreAppointmentId = null,
    ): bool {
        if (blank($startDate)) {
            return false;
        }

        return count(array_intersect(
            self::expandDateRange($startDate, $endDate),
            self::unavailableDatesForClinic($clinicId, $ignoreAppointmentId),
        )) > 0;
    }

    /**
     * @return list<string>
     */
    protected static function expandDateRange(CarbonInterface|string $startDate, CarbonInterface|string|null $endDate = null): array
    {
        $start = self::normalizeToCarbon($startDate)->startOfDay();
        $end = filled($endDate)
            ? self::normalizeToCarbon($endDate)->startOfDay()
            : $start->copy();

        if ($end->lt($start)) {
            [$start, $end] = [$end, $start];
        }

        $dates = [];

        while ($start->lte($end)) {
            $dates[] = $start->toDateString();
            $start->addDay();
        }

        return $dates;
    }

    protected static function unavailableAppointmentQuery(int|string $clinicId, int|string|null $ignoreAppointmentId = null): Builder
    {
        return CalendarAppointment::query()
            ->where('ClinicId', $clinicId)
            ->where('NotAvailable', true)
            ->when(
                filled($ignoreAppointmentId),
                fn (Builder $query) => $query->whereKeyNot($ignoreAppointmentId),
            );
    }

    protected static function normalizeToCarbon(CarbonInterface|string $date): Carbon
    {
        if ($date instanceof CarbonInterface) {
            return Carbon::instance($date);
        }

        return Carbon::parse($date, config('app.timezone'));
    }
}
