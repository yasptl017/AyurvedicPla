@php
    /** @var \App\Models\PatientHistory $record */
    $diseases      = $record->diseases->pluck('Name')->filter()->values();
    $symptoms      = $record->symptoms->pluck('Name')->filter()->values();
    $prescriptions = $record->prescriptions;
@endphp

<div style="font-size:12px;line-height:1.5;color:inherit;padding:4px 0;display:flex;flex-direction:column;gap:6px;">

    {{-- Row 1: Diseases | Symptoms --}}
    <table style="width:100%;border-collapse:collapse;">
        <tbody>
            <tr>
                @if ($diseases->isNotEmpty())
                <td style="padding:0 12px 0 0;vertical-align:top;width:50%;">
                    <span style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;">Diseases &nbsp;</span>
                    @foreach ($diseases as $d)
                        <span style="display:inline-block;background:#fee2e2;color:#b91c1c;border-radius:999px;padding:1px 8px;font-size:11px;font-weight:500;margin:1px 2px 1px 0;">{{ $d }}</span>
                    @endforeach
                </td>
                @endif
                @if ($symptoms->isNotEmpty())
                <td style="padding:0;vertical-align:top;">
                    <span style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;">Symptoms &nbsp;</span>
                    @foreach ($symptoms as $s)
                        <span style="display:inline-block;background:#fef3c7;color:#92400e;border-radius:999px;padding:1px 8px;font-size:11px;margin:1px 2px 1px 0;">{{ $s }}</span>
                    @endforeach
                </td>
                @endif
            </tr>
        </tbody>
    </table>

    {{-- Row 2: Medicines table --}}
    @if ($prescriptions->isNotEmpty())
    <div style="border:1px solid #d1d5db;border-radius:6px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f3f4f6;border-bottom:1px solid #d1d5db;">
                    <th style="padding:5px 8px;text-align:left;font-size:11px;font-weight:600;color:#374151;border-right:1px solid #d1d5db;">#</th>
                    <th style="padding:5px 8px;text-align:left;font-size:11px;font-weight:600;color:#374151;border-right:1px solid #d1d5db;">Medicine</th>
                    <th style="padding:5px 8px;text-align:left;font-size:11px;font-weight:600;color:#374151;border-right:1px solid #d1d5db;">Form</th>
                    <th style="padding:5px 8px;text-align:left;font-size:11px;font-weight:600;color:#374151;border-right:1px solid #d1d5db;">Dose</th>
                    <th style="padding:5px 8px;text-align:left;font-size:11px;font-weight:600;color:#374151;border-right:1px solid #d1d5db;">Time</th>
                    <th style="padding:5px 8px;text-align:left;font-size:11px;font-weight:600;color:#374151;border-right:1px solid #d1d5db;">Qty</th>
                    <th style="padding:5px 8px;text-align:left;font-size:11px;font-weight:600;color:#374151;border-right:1px solid #d1d5db;">Anupana</th>
                    <th style="padding:5px 8px;text-align:right;font-size:11px;font-weight:600;color:#374151;">₹</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($prescriptions as $p)
                <tr style="{{ $loop->even ? 'background:#f9fafb;' : 'background:#fff;' }}{{ !$loop->last ? 'border-bottom:1px solid #e5e7eb;' : '' }}">
                    <td style="padding:4px 8px;color:#9ca3af;font-size:11px;border-right:1px solid #e5e7eb;">{{ $loop->iteration }}</td>
                    <td style="padding:4px 8px;font-weight:600;color:#111827;border-right:1px solid #e5e7eb;">{{ $p->medicine?->Name ?: '—' }}</td>
                    <td style="padding:4px 8px;color:#6b7280;border-right:1px solid #e5e7eb;">{{ $p->MedicineFormName ?: '—' }}</td>
                    <td style="padding:4px 8px;color:#6b7280;white-space:nowrap;border-right:1px solid #e5e7eb;">{{ $p->Dose ?: '—' }}</td>
                    <td style="padding:4px 8px;color:#6b7280;border-right:1px solid #e5e7eb;">{{ $p->TimeOfAdministration ?: '—' }}</td>
                    <td style="padding:4px 8px;color:#6b7280;white-space:nowrap;border-right:1px solid #e5e7eb;">{{ $p->Duration ?: '—' }}</td>
                    <td style="padding:4px 8px;color:#6b7280;border-right:1px solid #e5e7eb;">{{ $p->Anupana ?: '—' }}</td>
                    <td style="padding:4px 8px;text-align:right;color:#6b7280;">{{ ($p->Amount && $p->Amount != 0) ? $p->Amount : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Row 3: Fees · Next Visit · Remark · Note --}}
    @php
        $metaItems = [];
        if ($record->ConsultationFee || $record->MedicinesFee)
            $metaItems[] = ['label' => 'Fees', 'value' => 'Consult ₹' . number_format($record->ConsultationFee ?? 0) . '  ·  Meds ₹' . number_format($record->MedicinesFee ?? 0)];
        if ($record->NextAppointmentDate)
            $metaItems[] = ['label' => 'Next Visit', 'value' => $record->NextAppointmentDate->format('d M Y')];
        if ($record->Remark)
            $metaItems[] = ['label' => 'Remark', 'value' => Str::limit($record->Remark, 100)];
        if ($record->Note)
            $metaItems[] = ['label' => 'Note', 'value' => Str::limit($record->Note, 100)];
    @endphp
    @if (count($metaItems))
    <div style="border:1px solid #d1d5db;border-radius:6px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <tbody>
                @foreach (array_chunk($metaItems, 2) as $row)
                <tr style="{{ !$loop->last ? 'border-bottom:1px solid #e5e7eb;' : '' }}">
                    @foreach ($row as $item)
                    <td style="padding:5px 10px;width:50%;vertical-align:top;{{ !$loop->last ? 'border-right:1px solid #e5e7eb;' : '' }}">
                        <span style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;">{{ $item['label'] }}: </span>
                        <span style="color:#374151;">{{ $item['value'] }}</span>
                    </td>
                    @endforeach
                    @if (count($row) === 1)
                    <td style="padding:5px 10px;width:50%;border-left:1px solid #e5e7eb;"></td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
