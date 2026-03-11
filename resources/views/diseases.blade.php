@php
    $sections = [
        'Introduction' => 'Introduction',
        'Purvaroopa' => 'Purvaroopa',
        'Do Dont' => 'DoDont',
        'Sadhyabadyatva' => 'Sadhyabadyatva',
        'Chikitsa Sutra' => 'ChikitsaSutra',
        'Samprapti' => 'Samprapti',
        'Upadrava' => 'Upadrava',
        'Panchakarma' => 'Panchakarma',
        'Causes' => 'Causes',
        'Arishta Laxana' => 'ArishtaLaxana',
        'Differential Diagnosis' => 'DifferentialDiagnosis',
        'Laboratory Investions' => 'LaboratoryInvestions',
    ];
@endphp

<div
    class="space-y-6"
>
    @foreach($diseases as $disease)
        <div class="border-b border-gray-200 pb-4 last:border-0 dark:border-gray-700">
            <!-- Header -->
            <h3 class="text-sm font-bold text-primary-600 uppercase mb-2 tracking-wide dark:text-primary-400">
                {{ $disease->Name}}
            </h3>

            <!-- Links Row -->
            @php
                $availableSections = collect($sections)->filter(
                    fn($columnKey) => filled($disease->toArray()[$columnKey] ?? null)
                );
            @endphp
            <div class="flex flex-wrap text-sm text-gray-600 dark:text-gray-300 items-center">
                @foreach($availableSections as $label => $columnKey)

                    <x-filament::modal width="6xl" id="disease-modal-{{ $disease->Id }}-{{ $loop->index }}">
                        <x-slot name="trigger">
                            <button
                                type="button"
                                class="hover:text-primary-600 hover:underline cursor-pointer transition-colors focus:outline-none"
                            >
                                {{ $label }}
                            </button>
                        </x-slot>
                        <x-slot name="heading">
                            {{ $disease->Name }} — {{ $label }}
                        </x-slot>

                        <div class="prose dark:prose-invert max-w-none" id="disease-content-{{ $disease->Id }}-{{ $loop->index }}">
                            {!! $disease->toArray()[$columnKey] !!}
                        </div>

                        <x-slot name="footer">
                            <div class="flex justify-end gap-3">
                                <x-filament::button
                                    color="gray"
                                    icon="heroicon-o-printer"
                                    onclick="
                                        var content = document.getElementById('disease-content-{{ $disease->Id }}-{{ $loop->index }}').innerHTML;
                                        var w = window.open('', '_blank');
                                        w.document.write('<html><head><title>{{ addslashes($disease->Name) }} — {{ addslashes($label) }}</title><style>body{font-family:sans-serif;padding:24px;max-width:900px;margin:auto}</style></head><body>' + content + '</body></html>');
                                        w.document.close();
                                        w.focus();
                                        w.print();
                                    "
                                >
                                    Print
                                </x-filament::button>

                                <x-filament::button
                                    color="gray"
                                    @click="$dispatch('close-modal', {id: 'disease-modal-{{ $disease->Id }}-{{ $loop->index }}'})"
                                >
                                    Close
                                </x-filament::button>
                            </div>
                        </x-slot>
                    </x-filament::modal>

                    @if(!$loop->last)
                        <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                    @endif

                @endforeach
            </div>
        </div>
    @endforeach

</div>
