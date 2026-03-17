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
    <div class="patient-details">
        <div class="detail-row detail-row--name">
            <div class="detail-pair">
                <span class="detail-label">Name:</span>
                <span class="detail-value">{{ $patientName }}</span>
            </div>
            <div class="detail-pair">
                <span class="detail-label">Date:</span>
                <span class="detail-value">{{ $patientDate }}</span>
            </div>
        </div>
        <div class="detail-row detail-row--address">
            <div class="detail-pair">
                <span class="detail-label">Weight:</span>
                <span class="detail-value">{{ $patientWeight }}</span>
            </div>
            <div class="detail-pair">
                <span class="detail-label">Address/City:</span>
                <span class="detail-value">{{ $patientAddress }}</span>
            </div>
        </div>
        <div class="detail-row detail-row--contact">
            <div class="detail-pair">
                <span class="detail-label">Age:</span>
                <span class="detail-value">{{ $patientAge }}</span>
            </div>
            <div class="detail-pair">
                <span class="detail-label">Mobile:</span>
                <span class="detail-value">{{ $patientMobile }}</span>
            </div>
        </div>
    </div>

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
            <p class="gujarati-text">ફરી બતાવવાની તારીખ: <u>{{ $history->NextAppointmentDate?->timezone(config('app.timezone'))->format('d/m/Y') }}</u></p>
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
