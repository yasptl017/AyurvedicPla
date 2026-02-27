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
            <div class="flex flex-wrap text-sm text-gray-600 dark:text-gray-300 items-center">
                @foreach($sections as $label => $columnKey)

                    <x-filament::modal width="6xl">
                        <x-slot name="trigger">
                            <button
                                type="button"
                                class="hover:text-primary-600 hover:underline cursor-pointer transition-colors focus:outline-none"
                            >
                                {{ $label }}
                            </button>
                        </x-slot>
                        <x-slot name="heading">
                            {{$label}}
                        </x-slot>


                        <div class="prose dark:prose-invert max-w-none">
                            {!! $disease->toArray()[$columnKey] !!}
                        </div>

                    </x-filament::modal>

                    @if(!$loop->last)
                        <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                    @endif

                @endforeach
            </div>
        </div>
    @endforeach

</div>
