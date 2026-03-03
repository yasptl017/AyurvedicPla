@php
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    /** @var \App\Models\PatientHistory $record */
    $diseases = $record->diseases->pluck('Name')->filter()->values();
    $symptoms = $record->symptoms->pluck('Name')->filter()->values();
    $modernSymptoms = $record->modernSymptoms->pluck('Name')->filter()->sort()->values();
    $prescriptions = $record->prescriptions;

    $excludedKeys = collect([
        'id',
        'patient_history_id',
        'patienthistoryid',
        'created_at',
        'updated_at',
        'createddate',
        'modifieddate',
        'deleteddate',
        'createdby',
        'modifiedby',
        'deletedby',
        'isdeleted',
    ]);

    $isMeaningful = fn ($value): bool => $value !== null && $value !== '' && $value !== false && $value !== 0 && $value !== [];

    $extractFilledFields = function (?Model $model) use ($excludedKeys, $isMeaningful): array {
        if (! $model) {
            return [];
        }

        return collect($model->attributesToArray())
            ->reject(fn ($value, $key) => $excludedKeys->contains(strtolower($key)))
            ->filter(fn ($value) => $isMeaningful($value))
            ->all();
    };

    $getFileUrl = function (?string $path): ?string {
        if (! filled($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', 'data:', '/'])) {
            return $path;
        }

        if (Str::startsWith($path, 'storage/')) {
            return asset($path);
        }

        try {
            return Storage::url($path);
        } catch (\Throwable $e) {
            return $path;
        }
    };

    $isImagePath = function (?string $path): bool {
        if (! filled($path)) {
            return false;
        }

        if (Str::startsWith($path, 'data:image/')) {
            return true;
        }

        $parsedPath = parse_url($path, PHP_URL_PATH) ?: $path;
        $extension = strtolower(pathinfo($parsedPath, PATHINFO_EXTENSION));

        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']);
    };

    $vitalData = $extractFilledFields($record->vital);
    $gynecData = $extractFilledFields($record->womenHistory);
    $rogaData = $extractFilledFields($record->rogaPariksa);
    $hetuData = $extractFilledFields($record->hetuPariksa);

    $enteredPanchakarmaDetails = $record->panchakarmas
        ->mapWithKeys(fn ($item) => [$item->Id => $item->pivot?->Detail])
        ->all();

    static $allPanchakarmas = null;
    if ($allPanchakarmas === null) {
        $allPanchakarmas = \App\Models\Panchakarma::query()
            ->select(['Id', 'Name'])
            ->orderBy('Name')
            ->get();
    }

    $panchakarmaFullData = $allPanchakarmas
        ->map(fn ($item) => [
            'name' => $item->Name,
            'detail' => $enteredPanchakarmaDetails[$item->Id] ?? null,
        ])
        ->values();

    $panchakarmaData = $panchakarmaFullData
        ->filter(fn ($item) => $isMeaningful($item['detail']))
        ->values();

    $patientReports = $record->patientRecords
        ->filter(fn ($item) => filled($item->capture))
        ->values();

    $sketches = $record->sketches
        ->filter(fn ($item) => filled($item->sketch))
        ->values();

    $captures = $record->captures
        ->filter(fn ($item) => filled($item->capture))
        ->values();

    $patientFiles = $record->patientFiles
        ->filter(fn ($item) => filled($item->File))
        ->values();

    $detailButtons = [];

    if (! empty($vitalData)) {
        $detailButtons['vital'] = 'Vital';
    }
    if (! empty($gynecData)) {
        $detailButtons['gynec'] = 'Gynec History';
    }
    if ($panchakarmaData->isNotEmpty()) {
        $detailButtons['panchakarma'] = 'Panchakarma';
    }
    if (! empty($rogaData)) {
        $detailButtons['roga_pariksa'] = 'Roga Pariksa';
    }
    if (! empty($hetuData)) {
        $detailButtons['hetu_pariksa'] = 'Hetu Pariksa';
    }
    if ($patientReports->isNotEmpty()) {
        $detailButtons['patient_reports'] = 'Patient Reports';
    }
    if ($sketches->isNotEmpty()) {
        $detailButtons['sketches'] = 'Sketches';
    }
    if ($captures->isNotEmpty()) {
        $detailButtons['captures'] = 'Captures';
    }
    if ($patientFiles->isNotEmpty()) {
        $detailButtons['patient_files'] = 'Patient Files';
    }

    $historyEditUrl = \App\Filament\App\Resources\Patients\Resources\PatientHistories\PatientHistoryResource::getUrl('edit', [
        'record' => $record->Id,
        'patient' => $record->PatientId,
    ]);
@endphp

<div
    x-data="{
        detailModal: null,
        formFrameLoading: false,
        formFrameReady: false,
        historyEditUrl: '{{ $historyEditUrl }}',
        formTabs: {
            vital: 'Vital',
            gynec: 'Gynec History',
            panchakarma: 'Panchakarma',
            roga_pariksa: 'RogaPariska',
            hetu_pariksa: 'HetuPariksa',
        },
        setDetailModal(tab) {
            this.detailModal = tab;

            if (this.formTabs[tab]) {
                this.formFrameLoading = true;
                this.formFrameReady = false;
                this.$nextTick(() => this.openFormTab(tab));
            } else {
                this.formFrameLoading = false;
                this.formFrameReady = false;
            }
        },
        openFormTab(tab) {
            const frame = this.$refs.historyFormFrame;
            if (! frame) {
                return;
            }

            frame.dataset.targetTab = tab;

            if (frame.getAttribute('src') !== this.historyEditUrl) {
                frame.setAttribute('src', this.historyEditUrl);

                return;
            }

            this.applyFrameLayout();
            this.hideFrameControls();
            this.activateFormTab(tab);
            this.formFrameLoading = false;
            this.formFrameReady = true;
        },
        onFormFrameLoad() {
            const frame = this.$refs.historyFormFrame;

            if (! frame) {
                return;
            }

            this.applyFrameLayout();
            this.hideFrameControls();
            this.activateFormTab(frame.dataset.targetTab || this.detailModal);
            this.formFrameLoading = false;
            this.formFrameReady = true;
        },
        normalizeLabel(value) {
            return (value || '')
                .replace(/[\u2022\u25CF]/g, '')
                .replace(/\s+/g, ' ')
                .trim()
                .toLowerCase();
        },
        applyFrameLayout() {
            const frame = this.$refs.historyFormFrame;
            const frameDoc = frame?.contentDocument;

            if (! frameDoc) {
                return;
            }

            const styleId = 'history-popup-frame-style';
            if (frameDoc.getElementById(styleId)) {
                return;
            }

            const style = frameDoc.createElement('style');
            style.id = styleId;
            style.textContent = `
                html, body {
                    margin: 0 !important;
                    padding: 0 !important;
                }
                .fi-sidebar,
                .fi-topbar,
                .fi-breadcrumbs,
                .fi-global-search,
                .fi-page-header,
                .fi-page-header-actions-ctn {
                    display: none !important;
                }
                .fi-main {
                    margin-inline-start: 0 !important;
                    padding-top: 0 !important;
                    padding-inline: 0.75rem !important;
                }
                .fi-main-ctn {
                    max-width: 100% !important;
                    padding: 0 !important;
                }
                .fi-page-header-main-ctn {
                    padding-top: 0 !important;
                    padding-bottom: 0 !important;
                    gap: 0.5rem !important;
                }
                .fi-page-main,
                .fi-page-content {
                    gap: 0.5rem !important;
                }
                .fi-form-actions, button[type='submit'] {
                    display: none !important;
                }
            `;
            frameDoc.head.appendChild(style);
        },
        hideFrameControls() {
            const frame = this.$refs.historyFormFrame;
            const frameDoc = frame?.contentDocument;

            if (! frameDoc) {
                return;
            }

            ['.fi-page-header', '.fi-page-header-actions-ctn', '.fi-form-actions'].forEach((selector) => {
                frameDoc.querySelectorAll(selector).forEach((el) => {
                    el.style.display = 'none';
                });
            });

            const hiddenText = ['delete', 'print', 'cancel', 'edit patient history'];
            frameDoc.querySelectorAll('button, a, h1, h2, h3, [role=heading]').forEach((el) => {
                const text = this.normalizeLabel(el.textContent);
                if (hiddenText.includes(text)) {
                    el.style.display = 'none';
                }
            });
        },
        activateFormTab(tab) {
            const frame = this.$refs.historyFormFrame;
            const frameDoc = frame?.contentDocument;

            if (! frameDoc || ! tab || ! this.formTabs[tab]) {
                return;
            }

            const targetLabel = this.normalizeLabel(this.formTabs[tab]);
            const candidates = Array.from(frameDoc.querySelectorAll('[role=tab]'));
            const target = candidates.find((el) => this.normalizeLabel(el.textContent) === targetLabel)
                || candidates.find((el) => this.normalizeLabel(el.textContent).startsWith(targetLabel));

            if (target) {
                target.click();
            }
        },
    }"
    @keydown.escape.window="detailModal = null"
    style="font-size:12px;line-height:1.5;color:inherit;padding:4px 0;display:flex;flex-direction:column;gap:6px;"
>
    @if (count($detailButtons))
        <div style="display:flex;flex-wrap:wrap;gap:6px;">
            @foreach ($detailButtons as $detailKey => $detailLabel)
                <button
                    type="button"
                    @click="setDetailModal('{{ $detailKey }}')"
                    style="padding:4px 10px;border:1px solid #d1d5db;border-radius:999px;background:#f9fafb;color:#374151;font-size:11px;font-weight:600;cursor:pointer;"
                >
                    {{ $detailLabel }}
                </button>
            @endforeach
        </div>
    @endif

    <div
        x-cloak
        x-show="detailModal !== null"
        x-transition.opacity.duration.150ms
        style="position:fixed;inset:0;z-index:9999;background:rgba(17,24,39,.55);"
    >
        <div
            @click.self="detailModal = null"
            style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;padding:16px;"
        >
            <div
                x-show="detailModal !== null"
                x-transition.scale.duration.150ms
                :style="formTabs[detailModal]
                    ? 'width:min(1500px,98vw);max-height:92vh;overflow:hidden;background:#ffffff;border-radius:12px;box-shadow:0 24px 48px rgba(0,0,0,.25);'
                    : 'width:min(980px,95vw);max-height:90vh;overflow:auto;background:#ffffff;border-radius:12px;box-shadow:0 24px 48px rgba(0,0,0,.25);'"
            >
                <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-bottom:1px solid #e5e7eb;position:sticky;top:0;background:#ffffff;z-index:1;">
                    <h3 style="margin:0;font-size:14px;font-weight:700;color:#111827;">
                        <span x-show="detailModal === 'vital'">Vital</span>
                        <span x-show="detailModal === 'gynec'">Gynec History</span>
                        <span x-show="detailModal === 'panchakarma'">Panchakarma</span>
                        <span x-show="detailModal === 'roga_pariksa'">Roga Pariksa</span>
                        <span x-show="detailModal === 'hetu_pariksa'">Hetu Pariksa</span>
                        <span x-show="detailModal === 'patient_reports'">Patient Reports</span>
                        <span x-show="detailModal === 'sketches'">Sketches</span>
                        <span x-show="detailModal === 'captures'">Captures</span>
                        <span x-show="detailModal === 'patient_files'">Patient Files</span>
                    </h3>
                    <button
                        type="button"
                        @click="detailModal = null"
                        style="border:none;background:transparent;color:#6b7280;font-size:20px;line-height:1;cursor:pointer;padding:0 4px;"
                    >
                        &times;
                    </button>
                </div>

                <div style="padding:14px;">
                    <div x-cloak x-show="formTabs[detailModal]" style="height:min(78vh, calc(92vh - 90px));position:relative;">
                        <div
                            x-show="formFrameLoading"
                            style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:#ffffff;border-radius:8px;color:#6b7280;font-size:12px;z-index:1;"
                        >
                            Loading form...
                        </div>
                        <iframe
                            x-ref="historyFormFrame"
                            @load="onFormFrameLoad"
                            title="Patient history form"
                            :style="'width:100%;height:100%;border:none;border-radius:8px;background:#fff;transition:opacity .12s ease;opacity:' + (formFrameReady ? '1' : '0') + ';'"
                        ></iframe>
                    </div>

                    @if ($patientReports->isNotEmpty())
                        <div x-cloak x-show="detailModal === 'patient_reports'" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px;">
                            @foreach ($patientReports as $item)
                                @php
                                    $path = $item->capture;
                                    $url = $getFileUrl($path);
                                @endphp
                                <div style="border:1px solid #e5e7eb;border-radius:8px;padding:10px;">
                                    <div style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;">Report {{ $loop->iteration }}</div>
                                    @if ($url)
                                        <a href="{{ $url }}" target="_blank" style="display:inline-block;margin:6px 0 8px;font-size:12px;color:#2563eb;text-decoration:underline;">Open file</a>
                                    @endif
                                    @if ($url && $isImagePath($path))
                                        <img src="{{ $url }}" alt="Patient report {{ $loop->iteration }}" style="width:100%;max-height:220px;object-fit:contain;border:1px solid #e5e7eb;border-radius:6px;background:#f9fafb;" />
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($sketches->isNotEmpty())
                        <div x-cloak x-show="detailModal === 'sketches'" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px;">
                            @foreach ($sketches as $item)
                                @php
                                    $path = $item->sketch;
                                    $url = $getFileUrl($path);
                                    $openUrl = route('patient.sketches.view', ['record' => $item->id]);
                                    $previewUrl = $url ?: $openUrl;
                                @endphp
                                <div style="border:1px solid #e5e7eb;border-radius:8px;padding:10px;">
                                    <div style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;">Sketch {{ $loop->iteration }}</div>
                                    @if ($openUrl)
                                        <a href="{{ $openUrl }}" target="_blank" style="display:inline-block;margin:6px 0 8px;font-size:12px;color:#2563eb;text-decoration:underline;">Open file</a>
                                    @endif
                                    @if ($previewUrl)
                                        <img src="{{ $previewUrl }}" alt="Sketch {{ $loop->iteration }}" style="width:100%;max-height:220px;object-fit:contain;border:1px solid #e5e7eb;border-radius:6px;background:#f9fafb;" />
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($captures->isNotEmpty())
                        <div x-cloak x-show="detailModal === 'captures'" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px;">
                            @foreach ($captures as $item)
                                @php
                                    $path = $item->capture;
                                    $url = $getFileUrl($path);
                                @endphp
                                <div style="border:1px solid #e5e7eb;border-radius:8px;padding:10px;">
                                    <div style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;">Capture {{ $loop->iteration }}</div>
                                    @if ($url)
                                        <a href="{{ $url }}" target="_blank" style="display:inline-block;margin:6px 0 8px;font-size:12px;color:#2563eb;text-decoration:underline;">Open file</a>
                                    @endif
                                    @if ($url)
                                        <img src="{{ $url }}" alt="Capture {{ $loop->iteration }}" style="width:100%;max-height:220px;object-fit:contain;border:1px solid #e5e7eb;border-radius:6px;background:#f9fafb;" />
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($patientFiles->isNotEmpty())
                        <div x-cloak x-show="detailModal === 'patient_files'" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px;">
                            @foreach ($patientFiles as $item)
                                @php
                                    $path = $item->File;
                                    $openUrl = route('patient.files.download', ['record' => $item->id]);
                                @endphp
                                <div style="border:1px solid #e5e7eb;border-radius:8px;padding:10px;">
                                    <div style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;">File {{ $loop->iteration }}</div>
                                    @if ($openUrl)
                                        <a href="{{ $openUrl }}" target="_blank" style="display:inline-block;margin:6px 0 8px;font-size:12px;color:#2563eb;text-decoration:underline;">Open file</a>
                                    @endif
                                    @if ($openUrl && $isImagePath($path))
                                        <img src="{{ $openUrl }}" alt="Patient file {{ $loop->iteration }}" style="width:100%;max-height:220px;object-fit:contain;border:1px solid #e5e7eb;border-radius:6px;background:#f9fafb;" />
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($diseases->isNotEmpty() || $symptoms->isNotEmpty() || $modernSymptoms->isNotEmpty())
        <table style="width:100%;border-collapse:collapse;">
            <tbody>
                <tr>
                    @if ($diseases->isNotEmpty())
                        <td style="padding:0 12px 0 0;vertical-align:top;width:33.33%;">
                            <span style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;">Diseases&nbsp;</span>
                            @foreach ($diseases as $d)
                                <span style="display:inline-block;background:#fee2e2;color:#b91c1c;border-radius:999px;padding:1px 8px;font-size:11px;font-weight:500;margin:1px 2px 1px 0;">{{ $d }}</span>
                            @endforeach
                        </td>
                    @endif
                    @if ($symptoms->isNotEmpty())
                        <td style="padding:0 12px 0 0;vertical-align:top;width:33.33%;">
                            <span style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;">Symptoms&nbsp;</span>
                            @foreach ($symptoms as $s)
                                <span style="display:inline-block;background:#fef3c7;color:#92400e;border-radius:999px;padding:1px 8px;font-size:11px;margin:1px 2px 1px 0;">{{ $s }}</span>
                            @endforeach
                        </td>
                    @endif
                    @if ($modernSymptoms->isNotEmpty())
                        <td style="padding:0;vertical-align:top;">
                            <span style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;">Modern Symptoms&nbsp;</span>
                            @foreach ($modernSymptoms as $ms)
                                <span style="display:inline-block;background:#dbeafe;color:#1d4ed8;border-radius:999px;padding:1px 8px;font-size:11px;margin:1px 2px 1px 0;">{{ $ms }}</span>
                            @endforeach
                        </td>
                    @endif
                </tr>
            </tbody>
        </table>
    @endif

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
                        <th style="padding:5px 8px;text-align:right;font-size:11px;font-weight:600;color:#374151;">Rs</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($prescriptions as $p)
                        <tr style="{{ $loop->even ? 'background:#f9fafb;' : 'background:#fff;' }}{{ ! $loop->last ? 'border-bottom:1px solid #e5e7eb;' : '' }}">
                            <td style="padding:4px 8px;color:#9ca3af;font-size:11px;border-right:1px solid #e5e7eb;">{{ $loop->iteration }}</td>
                            <td style="padding:4px 8px;font-weight:600;color:#111827;border-right:1px solid #e5e7eb;">{{ $p->medicine?->Name ?: '-' }}</td>
                            <td style="padding:4px 8px;color:#6b7280;border-right:1px solid #e5e7eb;">{{ $p->MedicineFormName ?: '-' }}</td>
                            <td style="padding:4px 8px;color:#6b7280;white-space:nowrap;border-right:1px solid #e5e7eb;">{{ $p->Dose ?: '-' }}</td>
                            <td style="padding:4px 8px;color:#6b7280;border-right:1px solid #e5e7eb;">{{ $p->TimeOfAdministration ?: '-' }}</td>
                            <td style="padding:4px 8px;color:#6b7280;white-space:nowrap;border-right:1px solid #e5e7eb;">{{ $p->Duration ?: '-' }}</td>
                            <td style="padding:4px 8px;color:#6b7280;border-right:1px solid #e5e7eb;">{{ $p->Anupana ?: '-' }}</td>
                            <td style="padding:4px 8px;text-align:right;color:#6b7280;">{{ ($p->Amount && $p->Amount != 0) ? $p->Amount : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @php
        $metaItems = [];
        if ($record->patient?->complain_of) {
            $metaItems[] = ['label' => 'Complain of', 'value' => Str::limit($record->patient->complain_of, 120)];
        }
        if ($record->patient?->history_of) {
            $metaItems[] = ['label' => 'History of', 'value' => Str::limit($record->patient->history_of, 120)];
        }
        if ($record->ConsultationFee || $record->MedicinesFee) {
            $metaItems[] = ['label' => 'Fees', 'value' => 'Consult Rs ' . number_format($record->ConsultationFee ?? 0) . ' | Meds Rs ' . number_format($record->MedicinesFee ?? 0)];
        }
        if ($record->NextAppointmentDate) {
            $metaItems[] = ['label' => 'Next Visit', 'value' => $record->NextAppointmentDate->format('d M Y')];
        }
        if ($record->Remark) {
            $metaItems[] = ['label' => 'Remark', 'value' => Str::limit($record->Remark, 100)];
        }
        if ($record->Note) {
            $metaItems[] = ['label' => 'Note', 'value' => Str::limit($record->Note, 100)];
        }
    @endphp
    @if (count($metaItems))
        <div style="border:1px solid #d1d5db;border-radius:6px;overflow:hidden;">
            <table style="width:100%;border-collapse:collapse;">
                <tbody>
                    @foreach (array_chunk($metaItems, 2) as $row)
                        <tr style="{{ ! $loop->last ? 'border-bottom:1px solid #e5e7eb;' : '' }}">
                            @foreach ($row as $item)
                                <td style="padding:5px 10px;width:50%;vertical-align:top;{{ ! $loop->last ? 'border-right:1px solid #e5e7eb;' : '' }}">
                                    <span style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;">{{ $item['label'] }}:</span>
                                    <span style="color:#374151;"> {{ $item['value'] }}</span>
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

