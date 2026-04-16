<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medications - {{ trim(implode(' ', array_filter([$patient->FirstName, $patient->MiddleName, $patient->LastName]))) ?: 'Patient' }}</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <style>
        /* Ensure header/footer/clinic details are not shown */
        .prescription-header, .prescription-footer { display: none !important; }
    </style>
</head>
<body onload="window.print()">
@php
    $patientName = trim(implode(' ', array_filter([
        $patient->FirstName,
        $patient->MiddleName,
        $patient->LastName,
    ])));
    $patientName = $patientName !== '' ? $patientName : 'Patient';
    $patientDate = $history->CreatedDate?->timezone(config('app.timezone'))->format('d/m/Y') ?? '-';
    $patientWeight = filled($patient->Weight) ? $patient->Weight . ' kg' : '-';
    $patientMobile = filled($patient->MobileNo) ? $patient->MobileNo : '-';
    $patientAddress = filled($patient->Address) ? $patient->Address : '-';
    $followUpDate = $history->NextAppointmentDate?->timezone(config('app.timezone'))->format('d/m/Y') ?? '-';
    $patientAge = '-';
    $patientBirthDateDisplay = null;
    $patientAgeShort = null;

    $ageReferenceDate = $history->CreatedDate
        ? \Illuminate\Support\Carbon::parse($history->CreatedDate)
        : now(config('app.timezone'));
    $patientBirthDate = filled($patient->BirthDate)
        ? \Illuminate\Support\Carbon::parse($patient->BirthDate)
        : null;

    if ($patientBirthDate) {
        $patientBirthDateDisplay = $patientBirthDate->timezone(config('app.timezone'))->format('d/m/Y');
    }

    if ($patientBirthDate && $patientBirthDate->lte($ageReferenceDate)) {
        $ageInterval = $patientBirthDate->diff($ageReferenceDate);
        $ageYears = $ageInterval->y;
        $ageMonths = $ageInterval->m;
        $ageDays = $ageInterval->d;

        if ($ageYears > 0) {
            $patientAgeShort = $ageYears . ' Y';
        } elseif ($ageMonths > 0) {
            $patientAgeShort = $ageMonths . 'M';
        } else {
            $patientAgeShort = $ageDays . ' D';
        }
    } else {
        $ageParts = [];

        if (filled($patient->AgeYear)) {
            $roundedAgeYear = (int) round((float) $patient->AgeYear);

            if ($roundedAgeYear > 0) {
                $ageParts[] = $roundedAgeYear . ' Y';
            }
        }

        if (filled($patient->AgeMonth)) {
            $roundedAgeMonth = (int) round((float) $patient->AgeMonth);

            if ($roundedAgeMonth > 0) {
                $ageParts[] = $roundedAgeMonth . 'M';
            }
        }

        if (count($ageParts) > 0) {
            $patientAgeShort = implode(' ', $ageParts);
        }
    }

    if ($patientBirthDateDisplay && $patientAgeShort) {
        $patientAge = $patientBirthDateDisplay . ' (' . $patientAgeShort . ')';
    } elseif ($patientBirthDateDisplay) {
        $patientAge = $patientBirthDateDisplay;
    } elseif ($patientAgeShort) {
        $patientAge = $patientAgeShort;
    }
@endphp

<div class="prescription-container">
    <table class="patient-details-table">
        <tbody>
            <tr>
                <td class="patient-details-cell patient-details-cell--left">
                    <span class="patient-details-label">Name:</span>
                    <span class="patient-details-value">{{ $patientName }}</span>
                </td>
                <td class="patient-details-cell patient-details-cell--right">
                    <span class="patient-details-label">Date:</span>
                    <span class="patient-details-value">{{ $patientDate }}</span>
                </td>
            </tr>
            <tr>
                <td class="patient-details-cell patient-details-cell--left">
                    <span class="patient-details-label">Mobile:</span>
                    <span class="patient-details-value">{{ $patientMobile }}</span>
                </td>
                <td class="patient-details-cell patient-details-cell--right">
                    <span class="patient-details-label">Age:</span>
                    <span class="patient-details-value">{{ $patientAge }}</span>
                </td>
            </tr>
            <tr>
                <td class="patient-details-cell patient-details-cell--left">
                    <span class="patient-details-label">Address:</span>
                    <span class="patient-details-value">{{ $patientAddress }}</span>
                </td>
                <td class="patient-details-cell patient-details-cell--right">
                    <span class="patient-details-label">Weight:</span>
                    <span class="patient-details-value">{{ $patientWeight }}</span>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="prescription-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Medicine</th>
                <th>Dose</th>
                <th>Time</th>
                <th>Qty</th>
                <th>Anupana</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($history->prescriptions as $idx => $p)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td><strong>{{ $p->medicine?->Name ?: '-' }}</strong> ({{ $p->MedicineFormName ?: '-' }})</td>
                    <td>{{ $p->Dose ?: '-' }}</td>
                    <td>{{ $p->TimeOfAdministration ?: '-' }}</td>
                    <td>{{ $p->Duration ?: '-' }}</td>
                    <td>{{ $p->Anupana ?: '-' }}</td>
                </tr>
            @endforeach
            <tr class="follow-up-row">
                <td class="follow-up-cell" colspan="6">ફરી બતાવવાની તારીખ: {{ $followUpDate }}</td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    window.addEventListener('afterprint', () => {
        setTimeout(() => { window.close(); }, 100);
    });
</script>
</body>
</html>
