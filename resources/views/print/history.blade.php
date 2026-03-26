<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Prescription - {{ $patient->FirstName ?? 'Patient' }}</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
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
    $patientAddress = filled($patient->Address) ? $patient->Address : '-';
    $patientAge = filled($patient->AgeYear) ? $patient->AgeYear . ' Years' : '-';
    $patientMobile = filled($patient->MobileNo) ? $patient->MobileNo : '-';
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
            <th>Medicine Name</th>
            <th>Dose</th>
            <th>Time of Administration</th>
            <th>Quantity</th>
            <th>Anupana</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($history->prescriptions as $prescription)
            <tr>
                <td><strong>{{ $prescription->medicine->Name }}</strong> ({{ $prescription->MedicineFormName }})</td>
                <td>{{ $prescription->Dose }}</td>
                <td>{{ $prescription->TimeOfAdministration }}</td>
                <td>{{ $prescription->Duration }}</td>
                <td>{{ $prescription->Anupana }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="follow-up">
            <p class="gujarati-text">ફરી બતાવવાની તારીખ: {{ $history->NextAppointmentDate?->timezone(config('app.timezone'))->format('d/m/Y') }}</p>
        </div>
    </div>
</div>

<script>
    window.addEventListener('afterprint', () => {
        setTimeout(() => {
            window.close();
        }, 100);
    });
</script>
</body>
</html>
