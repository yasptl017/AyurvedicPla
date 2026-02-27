<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Prescription - {{ $patient->FirstName ?? 'Patient' }}</title>
    <link rel="stylesheet" href="{{asset('style.css')}}"> <!-- Link to your CSS file -->
</head>
<body onload="window.print()">


<div class="prescription-container">
    <!-- Header Section -->
    <header class="header">
        <div class="header-center">
            <h1>{{$clinic->ClinicName}}</h1>
            <p>{{$clinic->Address}}</p>
            <p>Contact: {{$clinic->MobileNo}} | Email: {{$clinic->Email}}</p>
        </div>
    </header>

    <hr class="divider">

    <!-- Patient Info Section -->
    <div class="info-grid">
        <div class="info-item">
            <strong>Name:</strong> <span>{{$patient->FirstName}} {{$patient->MiddleName}} {{$patient->LastName}}</span>
        </div>
        <div class="info-item text-right">
            <strong>Date:</strong> <span>{{$history->CreatedDate}}</span>
        </div>
        <div class="info-item">

            <strong>Diagnosis:</strong> <span>
    {{ $history->diseases->pluck('Name')->join(' - ') }}
</span>
        </div>
        <div class="info-item text-right">
            <strong>Weight:</strong> <span>{{$patient->Weight}} kg</span>
        </div>
        <div class="info-item">
            <strong>Address/City:</strong> <span>{{$patient->Address}}</span>
        </div>
        <div class="info-item text-right">
            <strong>Age:</strong> <span>{{$patient->AgeYear}} Years</span>
        </div>
        <div class="info-item">
            <strong>Mobile:</strong> <span>{{$patient->MobileNo}}</span>
        </div>
    </div>

    <!-- Prescription Table -->
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
        @foreach($history->prescriptions as $prescription)
            <tr>
                <td><strong>{{$prescription->medicine->Name}}</strong> ({{$prescription->MedicineFormName}})</td>
                <td>{{$prescription->Dose}}</td>
                <td>{{$prescription->TimeOfAdministration}}</td>
                <td>{{$prescription->Duration}}</td>
                <td>{{$prescription->Anupana}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <!-- Footer Section -->
    <div class="footer">
        <div class="follow-up">
            <p class="gujarati-text">ફરી બતાવવાની તારીખ: <u>{{$history->NextAppointmentDate?->format('d-m-Y')}}</u></p>
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

