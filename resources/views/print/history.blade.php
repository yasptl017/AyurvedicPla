<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Prescription - {{ $patient->FirstName ?? 'Patient' }}</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body onload="window.print()">
<div class="prescription-container">
    <div class="info-grid">
        <div class="info-item">
            <strong>Name:</strong>
            <span>{{ $patient->FirstName }} {{ $patient->MiddleName }} {{ $patient->LastName }}</span>
        </div>
        <div class="info-item text-right">
            <strong>Date:</strong>
            <span>{{ $history->CreatedDate?->format('d-m-Y') }}</span>
        </div>
        <div class="info-item">
            <strong>Diagnosis:</strong>
            <span>{{ $history->diseases->pluck('Name')->join(' - ') }}</span>
        </div>
        <div class="info-item text-right">
            <strong>Weight:</strong>
            <span>{{ $patient->Weight }} kg</span>
        </div>
        <div class="info-item">
            <strong>Address/City:</strong>
            <span>{{ $patient->Address }}</span>
        </div>
        <div class="info-item text-right">
            <strong>Age:</strong>
            <span>{{ $patient->AgeYear }} Years</span>
        </div>
        <div class="info-item">
            <strong>Mobile:</strong>
            <span>{{ $patient->MobileNo }}</span>
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
            <p class="gujarati-text">ફરી બતાવવાની તારીખ: <u>{{ $history->NextAppointmentDate?->format('d-m-Y') }}</u></p>
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
