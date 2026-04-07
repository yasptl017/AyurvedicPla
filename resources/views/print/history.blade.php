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
    $clinicTiming = filled($clinic?->ClinicTiming) ? $clinic->ClinicTiming : null;
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
    @if ($clinicName || $clinicAddress || $clinicEmail || $clinicPhone1 || $clinicPhone2 || $clinicTiming || $doctorName || $doctorRegistrationNumber)
        <div class="prescription-header">
            <div class="prescription-header__top">
                <div class="prescription-header__top-item prescription-header__top-item--left">
                    @if ($doctorName)
                        <span class="prescription-header__doctor">{{ $doctorName }}</span>
                    @endif

                    @if ($doctorRegistrationNumber)
                        <p class="prescription-header__registration-line">Reg. No.: {{ $doctorRegistrationNumber }}</p>
                    @endif
                </div>

                <div class="prescription-header__top-item prescription-header__top-item--center">
                </div>

                <div class="prescription-header__top-item prescription-header__top-item--right">
                    @if ($clinicPhone1)
                        <span class="prescription-header__mobile">M: {{ $clinicPhone1 }}</span>
                    @endif

                    @if ($clinicTiming)
                        <p class="prescription-header__timing">{{ $clinicTiming }}</p>
                    @endif
                </div>
            </div>

            @if ($clinicName)
                <h1 class="prescription-header__title">{{ $clinicName }}</h1>
            @endif

            @if ($clinicPhone2 || $clinicEmail || $clinicLogo)
                <div class="prescription-header__details">
                    @if ($clinicLogo)
                        <div class="prescription-header__logo-wrap">
                            <img src="{{ $clinicLogo }}" alt="Clinic Logo" class="prescription-header__logo">
                        </div>
                    @endif

                    @if ($clinicPhone2)
                        <p class="prescription-header__line">M: {{ $clinicPhone2 }}</p>
                    @endif

                    @if ($clinicEmail)
                        <p class="prescription-header__line">{{ $clinicEmail }}</p>
                    @endif
                </div>
            @endif
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
                <th>S. No.</th>
                <th>Medicine Name</th>
                <th>Dose</th>
                <th>Time of Administration</th>
                <th>Quantity</th>
                <th>Anupana</th>
            </tr>
            </thead>

            @php
                $prescriptions = $history->prescriptions;
                $total = $prescriptions->count();
            @endphp

            @if ($total === 0)
                <tbody class="no-break">
                <tr class="follow-up-row">
                    <td class="follow-up-cell" colspan="6">ફરી બતાવવાની તારીખ: {{ $history->NextAppointmentDate?->timezone(config('app.timezone'))->format('d/m/Y') }}</td>
                </tr>
                </tbody>
            @else
                <tbody>
                @foreach ($prescriptions as $idx => $prescription)
                    @if ($idx < $total - 1)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td><strong>{{ $prescription->medicine->Name }}</strong> ({{ $prescription->MedicineFormName }})</td>
                            <td>{{ $prescription->Dose }}</td>
                            <td>{{ $prescription->TimeOfAdministration }}</td>
                            <td>{{ $prescription->Duration }}</td>
                            <td>{{ $prescription->Anupana }}</td>
                        </tr>
                    @endif
                @endforeach
                </tbody>

                {{-- Last medicine + follow-up grouped to avoid page-break between them --}}
                <tbody class="no-break">
                @php $last = $prescriptions->last(); @endphp
                <tr>
                    <td>{{ $total }}</td>
                    <td><strong>{{ $last->medicine->Name }}</strong> ({{ $last->MedicineFormName }})</td>
                    <td>{{ $last->Dose }}</td>
                    <td>{{ $last->TimeOfAdministration }}</td>
                    <td>{{ $last->Duration }}</td>
                    <td>{{ $last->Anupana }}</td>
                </tr>

                <tr class="follow-up-row">
                    <td class="follow-up-cell" colspan="6">ફરી બતાવવાની તારીખ: {{ $history->NextAppointmentDate?->timezone(config('app.timezone'))->format('d/m/Y') }}</td>
                </tr>
                </tbody>
            @endif
        </table>

    <div class="prescription-footer">
        <div class="prescription-footer__separator"></div>

        @if (count($warningFields))
            <div class="prescription-warnings">
                @foreach ($warningFields as $warningField)
                    <p class="prescription-warning">{{ $warningField }}</p>
                @endforeach
            </div>
        @endif

        @if ($clinicAddress)
            <p class="prescription-footer__address">{{ $clinicAddress }}</p>
        @endif
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
