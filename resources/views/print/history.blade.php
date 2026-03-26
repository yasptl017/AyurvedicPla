<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Prescription - {{ trim(implode(' ', array_filter([$patient->FirstName, $patient->MiddleName, $patient->LastName]))) ?: 'Patient' }}</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body onload="window.print()">
@php
    $clinicName = filled($clinic?->ClinicName) ? $clinic->ClinicName : null;
    $clinicAddress = filled($clinic?->Address) ? $clinic->Address : null;
    $clinicEmail = filled($clinic?->Email) ? $clinic->Email : null;
    $clinicPhone1 = filled($clinic?->MobileNo) ? $clinic->MobileNo : null;
    $clinicPhone2 = filled($clinic?->MobileNo2) ? $clinic->MobileNo2 : null;
    $doctorName = filled($clinic?->DoctorName) ? $clinic->DoctorName : null;
    $doctorRegistrationNumber = filled($clinic?->RegistrationNo) ? $clinic->RegistrationNo : null;
    $warningFields = array_values(array_filter([
        filled($clinic?->WarningField1) ? $clinic->WarningField1 : null,
        filled($clinic?->WarningField2) ? $clinic->WarningField2 : null,
    ]));
    $clinicLogo = null;

    if (filled($clinic?->Logo)) {
        if (\Illuminate\Support\Str::startsWith($clinic->Logo, ['http://', 'https://', '/storage/'])) {
            $clinicLogo = $clinic->Logo;
        } else {
            $clinicLogo = \Illuminate\Support\Facades\Storage::disk('public')->url($clinic->Logo);
        }
    }

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
    @if ($clinicName || $clinicAddress || $clinicEmail || $clinicPhone1 || $clinicPhone2 || $doctorName || $doctorRegistrationNumber || $clinicLogo)
        <div class="prescription-header">
            @if ($clinicLogo)
                <div class="prescription-header__logo-wrap">
                    <img src="{{ $clinicLogo }}" alt="Clinic Logo" class="prescription-header__logo">
                </div>
            @endif

            <div class="prescription-header__content">
                @if ($clinicName)
                    <h1 class="prescription-header__title">{{ $clinicName }}</h1>
                @endif

                @if ($clinicAddress)
                    <p class="prescription-header__line">{{ $clinicAddress }}</p>
                @endif

                @if ($doctorName || $doctorRegistrationNumber)
                    <p class="prescription-header__line">
                        @if ($doctorName)
                            <span>{{ $doctorName }}</span>
                        @endif
                        @if ($doctorRegistrationNumber)
                            <span class="prescription-header__inline-item"><strong>R.N.:</strong> {{ $doctorRegistrationNumber }}</span>
                        @endif
                    </p>
                @endif

                @if ($clinicPhone1 || $clinicPhone2 || $clinicEmail)
                    <p class="prescription-header__line">
                        @if ($clinicPhone1)
                            <span><strong>Phone:</strong> {{ $clinicPhone1 }}</span>
                        @endif
                        @if ($clinicPhone2)
                            <span class="prescription-header__inline-item"><strong>Phone:</strong> {{ $clinicPhone2 }}</span>
                        @endif
                        @if ($clinicEmail)
                            <span class="prescription-header__inline-item"><strong>Email:</strong> {{ $clinicEmail }}</span>
                        @endif
                    </p>
                @endif
            </div>
        </div>
    @endif

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
            @if (count($warningFields))
                <div class="prescription-warnings">
                    @foreach ($warningFields as $warningField)
                        <p class="prescription-warning">* {{ $warningField }}</p>
                    @endforeach
                </div>
            @endif
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
