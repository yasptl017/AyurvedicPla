<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prakruti Analysis - {{ trim(implode(' ', array_filter([$patient->FirstName, $patient->MiddleName, $patient->LastName]))) ?: 'Patient' }}</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <style>
        .prescription-header, .prescription-footer { display: none !important; }

        .exam-section {
            margin-top: 16px;
        }

        .exam-section__title {
            margin: 0 0 6px;
            padding-bottom: 4px;
            font-size: 16px;
            font-weight: 700;
            color: #b91c1c;
            border-bottom: 2px solid #2563eb;
        }

        .exam-table {
            width: 100%;
            border-collapse: collapse;
        }

        .exam-table th,
        .exam-table td {
            border: 1px solid #d5d5d5;
            padding: 6px 8px;
            text-align: left;
            font-size: 13px;
            line-height: 1.3;
            vertical-align: top;
        }

        .exam-table th {
            background: #f8f8f8;
            font-weight: 600;
            width: 28%;
        }

        .exam-empty {
            color: #888;
            font-style: italic;
        }

        .prakruti-form-table th {
            width: 22%;
        }

        .prakruti-form-table .dosha-tag {
            display: inline-block;
            font-weight: 700;
            margin-right: 4px;
        }

        .prakruti-form-table .dosha-tag--vat { color: #7c3aed; }
        .prakruti-form-table .dosha-tag--pit { color: #b91c1c; }
        .prakruti-form-table .dosha-tag--kaf { color: #0369a1; }

        .prakruti-checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #666;
            margin-right: 6px;
            vertical-align: middle;
            text-align: center;
            line-height: 11px;
            font-size: 10px;
            font-weight: 700;
        }

        .prakruti-checkbox--checked {
            background: #16a34a;
            border-color: #16a34a;
            color: #fff;
        }

        .prakruti-option--selected {
            font-weight: 700;
        }
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
    $patientMobile = filled($patient->MobileNo) ? $patient->MobileNo : '-';
    $patientDate = now()->timezone(config('app.timezone'))->format('d/m/Y');

    $textOrDash = fn ($value): string => filled($value) ? (string) $value : '-';

    $doshaTagClass = function (string $dosha): string {
        $dosha = strtoupper($dosha);

        return match (true) {
            str_contains($dosha, 'VAT') => 'dosha-tag--vat',
            str_contains($dosha, 'PIT') => 'dosha-tag--pit',
            str_contains($dosha, 'KAF') || str_contains($dosha, 'KAPHA') => 'dosha-tag--kaf',
            default => '',
        };
    };

    $prakrutiRows = [
        ['label' => 'Vata Count', 'value' => $textOrDash($prakruti?->VatCount)],
        ['label' => 'Vata %', 'value' => filled($prakruti?->VatPercentage) ? $prakruti->VatPercentage.'%' : '-'],
        ['label' => 'Pitta Count', 'value' => $textOrDash($prakruti?->PitCount)],
        ['label' => 'Pitta %', 'value' => filled($prakruti?->PitPercentage) ? $prakruti->PitPercentage.'%' : '-'],
        ['label' => 'Kapha Count', 'value' => $textOrDash($prakruti?->KufCount)],
        ['label' => 'Kapha %', 'value' => filled($prakruti?->KufPercentage) ? $prakruti->KufPercentage.'%' : '-'],
        ['label' => 'Total', 'value' => $textOrDash($prakruti?->Total)],
    ];
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
        </tr>
        </tbody>
    </table>

    <div class="exam-section">
        <h2 class="exam-section__title">Prakruti Assessment Form</h2>
        @if ($fieldsets->isEmpty())
            <p class="exam-empty">No Prakruti assessment questions configured.</p>
        @else
            <table class="exam-table prakruti-form-table">
                <tbody>
                @foreach ($fieldsets as $groupName => $items)
                    @php $selectedId = $responses[$groupName] ?? null; @endphp
                    <tr>
                        <th>{{ $groupName }}</th>
                        <td>
                            @foreach ($items as $item)
                                @php $isSelected = filled($selectedId) && (string) $selectedId === (string) $item['id']; @endphp
                                <span class="prakruti-checkbox {{ $isSelected ? 'prakruti-checkbox--checked' : '' }}">{{ $isSelected ? '✓' : '' }}</span><span class="dosha-tag {{ $doshaTagClass($item['dosha']) }} {{ $isSelected ? 'prakruti-option--selected' : '' }}">({{ $item['dosha'] }})</span> <span class="{{ $isSelected ? 'prakruti-option--selected' : '' }}">{{ $item['symptoms'] }}</span><br>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="exam-section">
        <h2 class="exam-section__title">Prakruti Analysis - Results</h2>
        @if (! $prakruti)
            <p class="exam-empty">No Prakruti Analysis data recorded.</p>
        @else
            <table class="exam-table">
                <tbody>
                @foreach (array_chunk($prakrutiRows, 2) as $pair)
                    <tr>
                        <th>{{ $pair[0]['label'] }}</th>
                        <td>{{ $pair[0]['value'] }}</td>
                        @if (isset($pair[1]))
                            <th>{{ $pair[1]['label'] }}</th>
                            <td>{{ $pair[1]['value'] }}</td>
                        @else
                            <th></th>
                            <td></td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

<script>
    window.addEventListener('afterprint', () => {
        setTimeout(() => { window.close(); }, 100);
    });
</script>
</body>
</html>
