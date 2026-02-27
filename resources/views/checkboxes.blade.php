<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            globalSymptoms: $wire.entangle('data.symptoms')
        }"
        class="flex flex-wrap gap-3"
    >
        {{-- Load the options specific to this row using $get() --}}
        @foreach($get('symptoms_options') as $symptom)
            <label>
                <x-filament::input.checkbox x-model="globalSymptoms" value="{{$symptom['Id']}}"/>

                <span>
                                {{$symptom['Name']}} ({{$symptom['NameEnglish'] ?? ''}}) {{$symptom['NameGujarati'] ?? ''}}
                </span>
            </label>
        @endforeach
    </div>
</x-dynamic-component>
