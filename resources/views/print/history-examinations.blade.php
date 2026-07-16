<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examinations - {{ trim(implode(' ', array_filter([$patient->FirstName, $patient->MiddleName, $patient->LastName]))) ?: 'Patient' }}</title>
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
            padding: 3px 6px;
            text-align: left;
            font-size: 10.5px;
            line-height: 1.3;
            vertical-align: top;
        }

        .exam-table th {
            background: #f8f8f8;
            font-weight: 600;
            width: 32%;
        }

        .exam-table--two-col th {
            width: 22%;
        }

        .exam-empty {
            color: #888;
            font-style: italic;
        }

        .hetu-table td {
            font-size: 9.5px;
        }

        .hetu-table th {
            width: 26%;
        }

        @media print {
            .exam-section {
                page-break-inside: auto;
            }

            .exam-table tr {
                page-break-inside: avoid;
            }
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
    $patientDate = $history->CreatedDate?->timezone(config('app.timezone'))->format('d/m/Y') ?? '-';
    $patientMobile = filled($patient->MobileNo) ? $patient->MobileNo : '-';

    $roga = $history->rogaPariksa;
    $hetu = $history->hetuPariksa;
    $ashta = $history->astavidhyaPariksha;

    $yesNo = fn (?bool $value): string => $value ? 'Yes' : 'No';
    $textOrDash = fn (?string $value): string => filled($value) ? $value : '-';

    $rogaDhoshaRows = [
        ['label' => '1) Dhosha', 'value' => collect(['Vat' => $roga?->Vat, 'Pit' => $roga?->Pit, 'Kaf' => $roga?->Kaf])
            ->filter()->keys()->implode(', ') ?: '-'],
        ['label' => '2) Dooshya - Dhatu', 'value' => collect(['Rasa' => $roga?->Rasa, 'Rakta' => $roga?->Rakta, 'Mansa' => $roga?->Mansa, 'Meda' => $roga?->Meda, 'Asthi' => $roga?->Asthi, 'Majja' => $roga?->Majja, 'Shukra' => $roga?->Shukra])
            ->filter()->keys()->implode(', ') ?: '-'],
        ['label' => '2) Dooshya - Upadhatu', 'value' => collect(['Stanya' => $roga?->Stanya, 'Raja' => $roga?->Raja, 'Kandara' => $roga?->Kandara, 'Sira' => $roga?->Sira, 'Dhamani' => $roga?->Dhamani, 'Twacha' => $roga?->Twacha, 'Snau' => $roga?->Snau])
            ->filter()->keys()->implode(', ') ?: '-'],
        ['label' => '2) Dooshya - Mala', 'value' => collect(['Poorisha' => $roga?->Poorisha, 'Mootra' => $roga?->Mootra, 'Sweda' => $roga?->Sweda, 'Kapha' => $roga?->Kapha, 'Pitta' => $roga?->Pitta, 'Khamala' => $roga?->Khamala, 'Kesha' => $roga?->Kesha, 'Nakha' => $roga?->Nakha, 'Akshisneha' => $roga?->Akshisneha, 'Loma' => $roga?->Loma, 'Shmashru' => $roga?->Shmashru])
            ->filter()->keys()->implode(', ') ?: '-'],
        ['label' => '3) Srotasa & Srotodushti Type', 'value' => collect(['Sanaga' => $roga?->Sanaga, 'Vimargagamana' => $roga?->Vimargagamana, 'Atipravrutti' => $roga?->Atipravrutti, 'Sira Granthi' => $roga?->Sira_granthi])
            ->filter()->keys()->implode(', ') ?: '-'],
        ['label' => '7) Rogamarga', 'value' => collect(['Koshtha' => $roga?->Koshtha, 'Shakha' => $roga?->Shakha, 'Marma' => $roga?->Marma])
            ->filter()->keys()->implode(', ') ?: '-'],
        ['label' => '4) Agni', 'value' => $textOrDash($roga?->Agni)],
        ['label' => '5) Udbhavasthana', 'value' => $textOrDash($roga?->Udbhavasthana)],
        ['label' => '6) Adhishthana', 'value' => $textOrDash($roga?->Adhishthana)],
        ['label' => '8) Vyadhi Swarupa - Onset', 'value' => $textOrDash($roga?->Vyadhi_swarupa1)],
        ['label' => '8) Vyadhi Swarupa - Severity', 'value' => $textOrDash($roga?->Vyadhi_swarupa2)],
        ['label' => '8) Vyadhi Swarupa - Chronicity', 'value' => $textOrDash($roga?->Vyadhi_swarupa3)],
        ['label' => 'Nidaana (Etiology)', 'value' => $textOrDash($roga?->Nidaana)],
        ['label' => 'Poorvarupa (Prodromal Symptoms)', 'value' => $textOrDash($roga?->Poorvarupa)],
        ['label' => 'Roopa (Signs & Symptoms)', 'value' => $textOrDash($roga?->Roopa)],
        ['label' => 'Sampraapti (Pathogenesis)', 'value' => $textOrDash($roga?->Sampraapti)],
        ['label' => 'Upashaya', 'value' => $textOrDash($roga?->Upashaya)],
        ['label' => 'Anupashaya', 'value' => $textOrDash($roga?->Anupashaya)],
        ['label' => 'Sambhavitha Vyadhi', 'value' => $textOrDash($roga?->Sambhavitha_vyadhi)],
        ['label' => 'Rogavinischaya (Final Diagnosis)', 'value' => $textOrDash($roga?->Rogavinischaya)],
        ['label' => 'Vyadhi Avastha (State)', 'value' => $textOrDash($roga?->Vyadhi_avastha1)],
        ['label' => 'Vyadhi Avastha (Depth)', 'value' => $textOrDash($roga?->Vyadhi_avastha2)],
        ['label' => 'Saadhyaasaadhyataa (Prognosis)', 'value' => $textOrDash($roga?->Prognosis)],
        ['label' => 'Upadrava (Complications)', 'value' => $textOrDash($roga?->Upadrava)],
        ['label' => 'Nidana (Detailed Etiology)', 'value' => $textOrDash($roga?->Nidana)],
    ];

    $ashtaRows = [
        ['label' => 'નાડી (Nadi)', 'value' => $textOrDash($ashta?->Nadi)],
        ['label' => 'મલ (Mal)', 'value' => $textOrDash($ashta?->Mal)],
        ['label' => 'મુત્ર (Mutra)', 'value' => $textOrDash($ashta?->Mutra)],
        ['label' => 'જીહવા (Jihva)', 'value' => $textOrDash($ashta?->Jihva)],
        ['label' => 'સ્પર્શ (Sparsha)', 'value' => $textOrDash($ashta?->Sparsha)],
        ['label' => 'ક્ષુધા (Kshudha)', 'value' => $textOrDash($ashta?->Kshudha)],
        ['label' => 'નિંદ્રા (Nindra)', 'value' => $textOrDash($ashta?->Nindra)],
        ['label' => 'આર્તવ - Regular', 'value' => $textOrDash($ashta?->AartavRegular)],
        ['label' => 'આર્તવ - Irregular', 'value' => $textOrDash($ashta?->AartavIrregular)],
        ['label' => 'Remark', 'value' => $textOrDash($ashta?->Remark)],
    ];

    $responses = $hetu?->Responses ?? [];
    $r = fn (string $key): ?string => filled($responses[$key] ?? null) ? (string) $responses[$key] : null;
    $rBool = fn (string $key): string => filled($responses[$key] ?? null) ? 'Yes' : 'No';
    $rChecked = function (array $keys) use ($responses): string {
        $checked = [];

        foreach ($keys as $key => $label) {
            if (! empty($responses[$key])) {
                $checked[] = $label;
            }
        }

        return count($checked) ? implode(', ', $checked) : '-';
    };
    $rDosha = function (int $i) use ($responses): string {
        $tags = [];

        foreach (['vat' => 'VAT', 'pit' => 'PIT', 'kuf' => 'KUF'] as $key => $label) {
            if (! empty($responses["q{$i}_{$key}"])) {
                $tags[] = $label;
            }
        }

        $status = $responses["q{$i}_status"] ?? null;

        if ($status === 'HitKar') {
            $tags[] = 'Hitkar';
        } elseif ($status === 'AhitKar') {
            $tags[] = 'Ahitkar';
        }

        return count($tags) ? implode(', ', $tags) : '-';
    };

    $hetuQuestions = [
        1 => ['label' => '1. Wake-up Time', 'value' => $textOrDash($r('Question1_Time'))],
        2 => ['label' => '2. Morning Exercise', 'value' => $textOrDash(trim(($r('Question2_ExcerciseYestNo') ?? '-').' '.($r('Question2_ExcerciseNames') ? '('.$r('Question2_ExcerciseNames').')' : '')))],
        3 => ['label' => '3. Addictions', 'value' => $textOrDash(trim(($rChecked(['Question3_Tobaco' => 'Tobacco', 'Question3_Masalo' => 'Masalo', 'Question3_Cigrate' => 'Cigarette', 'Question3_Alcohol' => 'Alcohol'])).($r('Question3_Other') ? ', '.$r('Question3_Other') : ''))) ],
        4 => ['label' => '4. Morning Tobacco/Cigarette (empty stomach)', 'value' => $textOrDash($r('Question4_TobacoMorningYesNo'))],
        5 => ['label' => '5. Morning Water (empty stomach)', 'value' => $textOrDash(trim(implode(' / ', array_filter([$r('Question5_WaterMorningYesNo'), $r('Question5_WaterMorningType'), $r('Question5_WaterQuantities')]))))],
        6 => ['label' => '6. Bowel Movement Timing', 'value' => $textOrDash($r('Question6_LatrineTime'))],
        7 => ['label' => '7. Bathing Time', 'value' => $textOrDash($r('Question7_BathBeforeOrAfterBreakFast'))],
        8 => ['label' => '8. Breakfast Time', 'value' => $textOrDash($r('Question8_BreakFastTime'))],
        9 => ['label' => '9. Hungry at Breakfast', 'value' => $textOrDash($r('Question9_BreakFastYesNo'))],
        10 => ['label' => '10. Breakfast without Hunger', 'value' => $textOrDash($r('Question10_BreakFastYesNo'))],
        11 => ['label' => '11. Breakfast Items', 'value' => $textOrDash($rChecked(['Question11_Tea' => 'Tea', 'Question11_Coffee' => 'Coffee', 'Question11_Milk' => 'Milk', 'Question11_Bhakhari' => 'Bhakhari/Rotli', 'Question11_BhakhariKhari' => 'Salty Bhakhari/Rotli', 'Question11_CoroBreakFast' => 'Dry Snacks', 'Question11_CarryWithOnion' => 'Vegetable w/ Onion/Tomato', 'Question11_Murmura' => 'Puffed Rice', 'Question11_Bread' => 'Bakery Items', 'Question11_Chatani' => 'Chutney/Pickle', 'Question11_EveningFood' => 'Evening Leftovers', 'Question11_FryFood' => 'Farsan/Others']))],
        12 => ['label' => '12. Tea/Milk/Juice Only', 'value' => $textOrDash(trim(implode(' | ', array_filter([$r('Question12_TeaOnlyYesNo'), $r('Question12_OnlyTea') ? 'Tea: '.$r('Question12_OnlyTea') : null, $r('Question12_OnlyMilk') ? 'Milk: '.$r('Question12_OnlyMilk') : null, $r('Question12_OnlyJuice') ? 'Juice: '.$r('Question12_OnlyJuice') : null]))))],
        13 => ['label' => '13. Dry Fruits/Nuts', 'value' => $textOrDash($r('Question13_NutsYesNo'))],
        14 => ['label' => '14. Water after Meal', 'value' => $textOrDash($r('Question14_WaterAfterLunch'))],
        15 => ['label' => '15. Occupation Type', 'value' => $textOrDash($rChecked(['Question15_SettingJob' => 'Sitting', 'Question15_StandingJob' => 'Standing', 'Question15_TravellingJob' => 'Travelling', 'Question15_SunLightJob' => 'Sunlight', 'Question15_SettingRoomJob' => 'Shade', 'Question15_AcJob' => 'AC']))],
        16 => ['label' => '16. Mid-morning Fruit/Snack', 'value' => $textOrDash(trim(($r('Question16_FruitYesNo') ?? '-').($r('Question16_Fruits') ? ' ('.$r('Question16_Fruits').')' : '')))],
        17 => ['label' => '17. Lunch Time', 'value' => $textOrDash($r('Question17_LunchTime'))],
        18 => ['label' => '18. Hungry / Routine at Lunch', 'value' => $textOrDash(trim(implode(' | ', array_filter([$r('Question18_LunchHugreyYesNo') ? 'Hungry: '.$r('Question18_LunchHugreyYesNo') : null, $r('Question18_TimeLunchYesNo') ? 'Routine: '.$r('Question18_TimeLunchYesNo') : null]))))],
        19 => ['label' => '19. Lunch Items', 'value' => $textOrDash($rChecked(['Question19_Guvar' => 'ગુવાર', 'Question19_Brijal' => 'રિગાણા', 'Question19_Tamato' => 'ટામેટા', 'Question19_Patato' => 'બટેટા', 'Question19_LadyFinger' => 'ભીંડો', 'Question19_Chana' => 'ચણા', 'Question19_Val' => 'વાલ', 'Question19_Vatana' => 'વટાણા', 'Question19_Adad' => 'અડદ', 'Question19_AdadPapad' => 'અડદ પાપડ', 'Question19_Dhosa' => 'ઢોસા', 'Question19_Marcha' => 'મરચા', 'Question19_ButterMilk' => 'છાશ', 'Question19_Curd' => 'દહીં', 'Question19_SugerCane' => 'ગોળ', 'Question19_Athanu' => 'અથાણું', 'Question19_DalBhat' => 'દાળ ભાત']))],
        20 => ['label' => '20. Daily/Frequent Intake', 'value' => $textOrDash($rChecked(['Question20_Gol' => 'ગોળ', 'Question20_Curd' => 'દહીં', 'Question20_ButterMilk' => 'છાસ', 'Question20_Athanu' => 'અથાણું', 'Question20_Spicies' => 'મરચા', 'Question20_Chatani' => 'ચટણી', 'Question20_Garlic' => 'લસણ', 'Question20_Onion' => 'ડુંગળી', 'Question20_PalkhniBhaji' => 'પાલખ', 'Question20_AdadPapad' => 'પાપડ', 'Question20_Rices' => 'ભાત', 'Question20_Tikhu' => 'તીખું', 'Question20_Khatu' => 'ખાટુ', 'Question20_Sour' => 'ખારું', 'Question20_KoroNasto' => 'કોરો નાસ્તો', 'Question20_LunchSleep' => 'બપોર નિદ્રા', 'Question20_LatenightWakeup' => 'રાત્રી જાગરણ', 'Question20_Sweet' => 'મીઠાઈ']))],
        21 => ['label' => '21. Daily Habits (Describe)', 'value' => $textOrDash($r('Question21_Details'))],
        22 => ['label' => '22. Raw Vegetables', 'value' => $textOrDash($rChecked(['Question22_Cabbage' => 'કોબી', 'Question22_Kakadi' => 'કાકડી', 'Question22_Tomato' => 'ટામેટા', 'Question22_Carrot' => 'ગાજર', 'Question22_LiliHatdar' => 'લીલી હળદળ', 'Question22_LadyFinger' => 'ભીંડો', 'Question22_SweetPatoto' => 'શકરિયા', 'Question22_LilaChana' => 'લીલા ચાના', 'Question22_LilaVatana' => 'લીલા વટાણા', 'Question22_LilaTuver' => 'લીલી તુવેર', 'Question22_LilaNuts' => 'લીલી સીંગ']))],
        23 => ['label' => '23. Garlic/Onion/Tomato/Lemon/Jaggery in Vegetables', 'value' => $textOrDash($r('Question23_VegitableInvalidYesNo'))],
        24 => ['label' => '24. After-meal Habit', 'value' => $textOrDash(trim(($r('Question24_AfterEatingYesNo') ?? '-').' '.($rChecked(['Question24_Mukhvas' => 'મુખવાસ', 'Question24_Nuts' => 'ખારીશીંગ', 'Question24_Daliya' => 'દાળિયા', 'Question24_Vatana' => 'વટાણા', 'Question24_Icecream' => 'આઇશક્રેમ', 'Question24_Fruit' => 'ફ્રૂટ', 'Question24_Soda' => 'સોડા']) !== '-' ? '('.$rChecked(['Question24_Mukhvas' => 'મુખવાસ', 'Question24_Nuts' => 'ખારીશીંગ', 'Question24_Daliya' => 'દાળિયા', 'Question24_Vatana' => 'વટાણા', 'Question24_Icecream' => 'આઇશક્રેમ', 'Question24_Fruit' => 'ફ્રૂટ', 'Question24_Soda' => 'સોડા']).')' : '')))],
        25 => ['label' => '25. Junk/Fried/Bakery Food', 'value' => $textOrDash($r('Question25_JunkFoodYesNo'))],
        26 => ['label' => '26. Afternoon Sleep', 'value' => $textOrDash(trim(($r('Question26_SleepAtNoon') ?? '-').($r('Question26_TimeInHour') ? ' ('.$r('Question26_TimeInHour').' hr)' : '')))],
        27 => ['label' => '27. Tea/Snack/Fruit after Nap', 'value' => $textOrDash(trim(($r('Question27_LunchAfterTea') ?? '-').($r('Question27_Names') ? ' ('.$r('Question27_Names').')' : '')))],
        28 => ['label' => '28. Evening Dinner Time', 'value' => $textOrDash($r('Question28_EveningDinner'))],
        29 => ['label' => '29. Evening Meal Details', 'value' => $textOrDash(trim(implode(' | ', array_filter([$r('Question29_DinnerNames'), $r('Question29_MilksInMl') ? 'Milk: '.$r('Question29_MilksInMl').'ml' : null, $r('Question29_KhichadiYesNo') ? 'Khichadi+Milk: '.$r('Question29_KhichadiYesNo') : null]))))],
        30 => ['label' => '30. Snack/Fruit/Milk/Water after Dinner', 'value' => $textOrDash($r('Question30_AfterDinnerSnackYesNo'))],
        31 => ['label' => '31. Total Daily Water Intake', 'value' => $textOrDash($r('Question31_WaterInDay'))],
        32 => ['label' => '32. Incompatible Food Combinations', 'value' => $textOrDash($rChecked(['Question32_KhichadiMilk' => 'Khichadi+Milk', 'Question32_Garlic' => 'Garlic/Onion/Tomato Veg+Milk', 'Question32_FruitMilk' => 'Fruit+Milk Juice', 'Question32_FruitSalad' => 'Fruit Salad/Milkshake', 'Question32_ButterAndMilk' => 'Buttermilk+Milk', 'Question32_ChatniWithMilk' => 'Chutney+Milk', 'Question32_HotWaterHoony' => 'Hot Water+Honey', 'Question32_UnSeasonalFruit' => 'Off-season Fruit', 'Question32_TakeFoodWithoutLatrine' => 'Eating w/o Bowel Movement']))],
        33 => ['label' => '33. Fruits Consumed', 'value' => $textOrDash($rChecked(['Question33_Banana' => 'કેળા', 'Question33_Apple' => 'સફરજન', 'Question33_Graps' => 'દ્રાક્ષ', 'Question33_WaterMeleon' => 'તરબૂચ', 'Question33_Coconut' => 'નાળિયર', 'Question33_Chiku' => 'ચીકુ', 'Question33_Pomegranate' => 'દાડમ', 'Question33_Mongo' => 'કેરી', 'Question33_Papiya' => 'પપેયો', 'Question33_Orange' => 'સંતરા', 'Question33_Gooseberry' => 'જામફળ', 'Question33_Jambu' => 'જાંબુ', 'Question33_SweetTeti' => 'સાકર ટેટી', 'Question33_SugarCane' => 'શેરડી', 'Question33_Stroberry' => 'સ્ટ્રોબરી', 'Question33_Ambala' => 'આંબળા', 'Question33_Kiwi' => 'કીવી', 'Question33_DragoanFruit' => 'દ્રગન ફ્રૂટ', 'Question33_Pinnepal' => 'પાઈનેપલ']))],
        34 => ['label' => '34. Other Daily Habit', 'value' => $textOrDash($r('Question34_Habbit'))],
        35 => ['label' => '35. Medical Details', 'value' => $textOrDash(trim(implode(' | ', array_filter([$r('Question35_OtherDisease') ? 'Disease: '.$r('Question35_OtherDisease') : null, $r('Question35_Medicines') ? 'Medicines: '.$r('Question35_Medicines') : null, $r('Question35_DiseaseTime') ? 'Duration: '.$r('Question35_DiseaseTime') : null]))))],
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
        <h2 class="exam-section__title">Roga Pariksha</h2>
        @if (! $roga)
            <p class="exam-empty">No Roga Pariksha data recorded.</p>
        @else
            <table class="exam-table">
                <tbody>
                @foreach ($rogaDhoshaRows as $row)
                    <tr>
                        <th>{{ $row['label'] }}</th>
                        <td>{{ $row['value'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="exam-section">
        <h2 class="exam-section__title">Ashtavidhya Pariksha</h2>
        @if (! $ashta)
            <p class="exam-empty">No Ashtavidhya Pariksha data recorded.</p>
        @else
            <table class="exam-table exam-table--two-col">
                <tbody>
                @foreach (array_chunk($ashtaRows, 2) as $pair)
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

    <div class="exam-section">
        <h2 class="exam-section__title">Hetu Pariksha</h2>
        @if (! $hetu || empty($responses))
            <p class="exam-empty">No Hetu Pariksha data recorded.</p>
        @else
            <table class="exam-table hetu-table exam-table--two-col">
                <tbody>
                @foreach (array_chunk($hetuQuestions, 2, true) as $pair)
                    @php $pairValues = array_values($pair); @endphp
                    <tr>
                        <th>{{ $pairValues[0]['label'] }}</th>
                        <td>{{ $pairValues[0]['value'] }}</td>
                        @if (isset($pairValues[1]))
                            <th>{{ $pairValues[1]['label'] }}</th>
                            <td>{{ $pairValues[1]['value'] }}</td>
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
