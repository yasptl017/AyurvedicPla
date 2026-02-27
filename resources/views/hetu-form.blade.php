<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            responses: {},

            init() {
                let state = $wire.get('{{ $getStatePath() }}');
                this.responses = (state && typeof state === 'object' && !Array.isArray(state))
                    ? JSON.parse(JSON.stringify(state))
                    : {};

                this.$watch('responses', (value) => {
                    $wire.$set('{{ $getStatePath() }}', JSON.parse(JSON.stringify(value)));
                });
            },

            // Safe getter
            get(key) {
                return this.responses && this.responses[key] ? this.responses[key] : null;
            },

            // Calculation Logic
            get totals() {
                let stats = { vat: 0, pit: 0, kuf: 0, hit: 0, ahit: 0 };
                for(let i=1; i<=35; i++) {
                    if(this.get('q'+i+'_vat')) stats.vat++;
                    if(this.get('q'+i+'_pit')) stats.pit++;
                    if(this.get('q'+i+'_kuf')) stats.kuf++;

                    let status = this.get('q'+i+'_status');
                    if(status === 'HitKar') stats.hit++;
                    if(status === 'AhitKar') stats.ahit++;
                }
                return stats;
            }
        }"
        class="flex flex-col gap-6"
    >

        {{-- LOOP THROUGH QUESTIONS --}}
        @foreach(range(1, 35) as $i)
            <div
                class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden transition-all duration-200 hover:ring-gray-950/10 dark:hover:ring-white/20">
                <div
                    class="flex flex-col lg:flex-row divide-y lg:divide-y-0 lg:divide-x divide-gray-200 dark:divide-white/10">

                    {{-- LEFT COLUMN: Questionnaire --}}
                    <div class="flex-1 p-6 flex flex-col gap-8">

                        {{-- Q1 --}}
                        @if($i == 1)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    1. તમે સવારે કેટલા વાગ્યે ઉઠો છો ?
                                </label>
                                <x-filament::input.wrapper class="max-w-xs">
                                    <x-filament::input type="time" x-model="responses.Question1_Time"/>
                                </x-filament::input.wrapper>
                                <p class="text-xs text-gray-500">Select Time</p>
                            </div>

                            {{-- Q2 --}}
                        @elseif($i == 2)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    2. સવારે કસરત કરો છો ?
                                </label>
                                <div class="flex flex-wrap gap-6">
                                    @foreach(['Yes', 'No'] as $opt)
                                        <label class="flex items-center gap-3 cursor-pointer group">
                                            <x-filament::input.radio value="{{ strtolower($opt) }}"
                                                                     x-model="responses.Question2_ExcerciseYestNo"/>
                                            <span
                                                class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-primary-600 transition-colors">{{ $opt }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <div x-show="responses.Question2_ExcerciseYestNo == 'yes'" x-transition class="pt-2">
                                    <x-filament::input.wrapper class="max-w-md">
                                        <x-filament::input.select x-model="responses.Question2_ExcerciseNames">
                                            <option value="">Select Excercise Name</option>
                                            <option value="Walking">Walking</option>
                                            <option value="Yoga">Yoga</option>
                                            <option value="Gym">Gym</option>
                                            <option value="Running">Running</option>
                                        </x-filament::input.select>
                                    </x-filament::input.wrapper>
                                </div>
                            </div>

                            {{-- Q3 --}}
                        @elseif($i == 3)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    3. તમારે વ્યાશન છે ? શેનું ?
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @foreach([
                                        'Tobaco' => 'તમાકુ (Tobacco)',
                                        'Masalo' => 'મસાલો/માવો (Masalo)',
                                        'Cigrate' => 'બીડી/સિગરેટ (Cigarette)',
                                        'Alcohol' => 'દારૂ (Alcohol)'
                                    ] as $key => $lbl)
                                        <label
                                            class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/5 cursor-pointer hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                                            <x-filament::input.checkbox x-model="responses.Question3_{{ $key }}"/>
                                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $lbl }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <x-filament::input.wrapper>
                                    <x-filament::input type="text" placeholder="Other"
                                                       x-model="responses.Question3_Other"/>
                                </x-filament::input.wrapper>
                            </div>

                            {{-- Q4 --}}
                        @elseif($i == 4)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    4. સવારે ઉઠીને નરણા તમાકુ કે બીડી પીવો છો ?
                                </label>
                                <div class="flex gap-8">
                                    @foreach(['Yes', 'No'] as $opt)
                                        <label class="flex items-center gap-3 cursor-pointer group">
                                            <x-filament::input.radio value="{{ $opt }}"
                                                                     x-model="responses.Question4_TobacoMorningYesNo"/>
                                            <span
                                                class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-primary-600">{{ $opt }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Q5 --}}
                        @elseif($i == 5)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    5. સવારે ઉઠીને નરણા પાણી પીવો છો ?
                                </label>
                                <div
                                    class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/5">
                                    <div class="space-y-3">
                                        <span
                                            class="text-xs font-bold uppercase tracking-wider text-gray-400">Response</span>
                                        <div class="flex gap-6">
                                            <label class="flex items-center gap-2">
                                                <x-filament::input.radio value="Yes"
                                                                         x-model="responses.Question5_WaterMorningYesNo"/>
                                                <span class="text-sm">Yes</span>
                                            </label>
                                            <label class="flex items-center gap-2">
                                                <x-filament::input.radio value="No"
                                                                         x-model="responses.Question5_WaterMorningYesNo"/>
                                                <span class="text-sm">No</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="space-y-3">
                                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Water Type</span>
                                        <div class="flex gap-6">
                                            <label class="flex items-center gap-2">
                                                <x-filament::input.radio value="Cold"
                                                                         x-model="responses.Question5_WaterMorningType"/>
                                                <span class="text-sm">ઠંડુ (Cold)</span>
                                            </label>
                                            <label class="flex items-center gap-2">
                                                <x-filament::input.radio value="Hot"
                                                                         x-model="responses.Question5_WaterMorningType"/>
                                                <span class="text-sm">ગરમ (Hot)</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="md:col-span-2 space-y-3">
                                        <span
                                            class="text-xs font-bold uppercase tracking-wider text-gray-400">Quantity</span>
                                        <x-filament::input.wrapper>
                                            <x-filament::input.select x-model="responses.Question5_WaterQuantities">
                                                <option value="">Select Water</option>
                                                <option value="1 Glass">1 Glass</option>
                                                <option value="2 Glasses">2 Glasses</option>
                                                <option value="3 Glasses">3 Glasses</option>
                                            </x-filament::input.select>
                                        </x-filament::input.wrapper>
                                    </div>
                                </div>
                            </div>

                            {{-- Q6 --}}
                        @elseif($i == 6)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    6. સંડાસ જવા ક્યારે જવું પડે છે ?
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @php
                                        $q6Opts = [
                                            'Wakeup' => 'ઉઠીને (After Waking)',
                                            'AfterWater' => 'પાણી પિએ પછી (After Water)',
                                            'AfterBreakFast' => 'નાસ્તા કર્યા પછી (After Breakfast)',
                                            'AfterIrregular' => 'અનિયમિત (Irregular)',
                                            'AfterMedicine' => 'કબજિયાત ની દવા લીધા પછી (After Constipation Medicine)',
                                            'AfterTabaco' => 'તમાકુ અથવા સિગરેટ/બીડી પીધા પછી (After Smoking/Tobacco)'
                                        ];
                                    @endphp
                                    @foreach($q6Opts as $val => $lbl)
                                        <label
                                            class="flex items-center gap-3 p-3 rounded-lg bg-gray-50/50 dark:bg-white/5 border border-transparent hover:border-primary-500 transition-all cursor-pointer">
                                            <x-filament::input.radio value="{{ $val }}"
                                                                     x-model="responses.Question6_LatrineTime"/>
                                            <span class="text-sm leading-tight">{{ $lbl }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Q7 --}}
                        @elseif($i == 7)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    7. ન્હવા ક્યારે જાવ છો ?
                                </label>
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <label
                                        class="flex items-center gap-3 p-3 rounded-lg border dark:border-white/5 flex-1 cursor-pointer">
                                        <x-filament::input.radio value="BeforeBreakFast"
                                                                 x-model="responses.Question7_BathBeforeOrAfterBreakFast"/>
                                        <span class="text-sm">નાસ્તા પહેલા (Before Breakfast)</span>
                                    </label>
                                    <label
                                        class="flex items-center gap-3 p-3 rounded-lg border dark:border-white/5 flex-1 cursor-pointer">
                                        <x-filament::input.radio value="AfterBreakFast"
                                                                 x-model="responses.Question7_BathBeforeOrAfterBreakFast"/>
                                        <span class="text-sm">નાસ્તા પછી (After Breakfast)</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Q8 --}}
                        @elseif($i == 8)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    8. નાસ્તો કેટલા વાગે કરો છો ?
                                </label>
                                <x-filament::input.wrapper class="max-w-xs">
                                    <x-filament::input type="time" x-model="responses.Question8_BreakFastTime"/>
                                </x-filament::input.wrapper>
                            </div>

                            {{-- Q9 --}}
                        @elseif($i == 9)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    9. સવારે નાસ્તો કરો છો ત્યારે ભૂખ લાગી હોય છે ?
                                </label>
                                <div class="flex gap-8">
                                    @foreach(['Yes', 'No'] as $opt)
                                        <label class="flex items-center gap-3 cursor-pointer group">
                                            <x-filament::input.radio value="{{ $opt }}"
                                                                     x-model="responses.Question9_BreakFastYesNo"/>
                                            <span class="text-sm">{{ $opt }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Q10 --}}
                        @elseif($i == 10)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    10. ભૂખ લાગ્યા વગર નાસ્તો કરો છો ?
                                </label>
                                <div class="flex gap-8">
                                    @foreach(['Yes', 'No'] as $opt)
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <x-filament::input.radio value="{{ $opt }}"
                                                                     x-model="responses.Question10_BreakFastYesNo"/>
                                            <span class="text-sm">{{ $opt }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Q11 --}}
                        @elseif($i == 11)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    11. નાસ્તા મા શુ લ્યો છો ?
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @php
                                        $q11Opts = [
                                            'Tea' => 'ચા (Tea)',
                                            'Coffee' => 'કોફી (Coffee)',
                                            'Milk' => 'દૂધ (Milk)',
                                            'Bhakhari' => 'ભાખરી/રોટલી (Bhakhari/Rotli)',
                                            'BhakhariKhari' => 'ભાખરી/રોટલી ખારી હોય (Salty Bhakhari/Rotli)',
                                            'CoroBreakFast' => 'કોરો નાસ્તો (Dry Snacks)',
                                            'CarryWithOnion' => 'શાક લશન, ડુંગળી કે ટામેટા વાળું (Vegetable with Garlic/Onion/Tomato)',
                                            'Murmura' => 'મમરા/ચવાણું (Puffed Rice/Snack mix)',
                                            'Bread' => 'પાઉં,બ્રેડ,ટૉસ,ખારી,બિસ્કિટ (Bakery items)',
                                            'Chatani' => 'ચટણી/ મરચા/અથાણું (Chutney/Chilli/Pickle)',
                                            'EveningFood' => 'સાંજનું વાસી (Evening Leftovers)',
                                            'FryFood' => 'પૌવા,ઉડલી,પરોઠા,ઢોકળા,બોર્નવીટા,ગાંઠિયા,ફરસાણ (Farsan/Others)'
                                        ];
                                    @endphp
                                    @foreach($q11Opts as $key => $lbl)
                                        <label
                                            class="flex items-center gap-3 p-2 rounded hover:bg-gray-50 dark:hover:bg-white/5 transition-colors cursor-pointer">
                                            <x-filament::input.checkbox x-model="responses.Question11_{{ $key }}"/>
                                            <span class="text-sm">{{ $lbl }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Q12 --}}
                        @elseif($i == 12)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    12. સવારે ફક્ત ચા કે દૂધ કે જ્યુશ પીવો છો ?
                                </label>
                                <div class="flex gap-6 pb-2 border-b dark:border-white/5">
                                    <label class="flex items-center gap-2">
                                        <x-filament::input.radio value="Yes"
                                                                 x-model="responses.Question12_TeaOnlyYesNo"/>
                                        <span class="text-sm">Yes</span>
                                    </label>
                                    <label class="flex items-center gap-2">
                                        <x-filament::input.radio value="No"
                                                                 x-model="responses.Question12_TeaOnlyYesNo"/>
                                        <span class="text-sm">No</span>
                                    </label>
                                </div>
                                <div class="grid gap-4">
                                    <x-filament::input.wrapper>
                                        <x-filament::input type="text" placeholder="એકલી ચા કપ મા (Tea only in cup)"
                                                           x-model="responses.Question12_OnlyTea"/>
                                    </x-filament::input.wrapper>
                                    <x-filament::input.wrapper>
                                        <x-filament::input type="text" placeholder="એકલું દૂધ કપ મા (Milk only in cup)"
                                                           x-model="responses.Question12_OnlyMilk"/>
                                    </x-filament::input.wrapper>
                                    <x-filament::input.wrapper>
                                        <x-filament::input type="text"
                                                           placeholder="એકલું જ્યુશ (કારેલા/દૂધી/એલોવેરા...)"
                                                           x-model="responses.Question12_OnlyJuice"/>
                                    </x-filament::input.wrapper>
                                </div>
                            </div>

                            {{-- Q13 --}}
                        @elseif($i == 13)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    13. સૂકો મેવો જેમકે કાજુ,બદામ,અખરોટ,અંજીર ખાવ છો ?
                                </label>
                                <div class="flex gap-8">
                                    @foreach(['Yes', 'No'] as $opt)
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <x-filament::input.radio value="{{ $opt }}"
                                                                     x-model="responses.Question13_NutsYesNo"/>
                                            <span class="text-sm">{{ $opt }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Q14 --}}
                        @elseif($i == 14)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    14. નાસ્તા કે જમ્યા પછી કેટલુ પાણી પીવો છો ?
                                </label>
                                <x-filament::input.wrapper class="max-w-md">
                                    <x-filament::input.select x-model="responses.Question14_WaterAfterLunch">
                                        <option value="">Select Water</option>
                                        <option value="None">None</option>
                                        <option value="Little">Little</option>
                                        <option value="Full">Full</option>
                                    </x-filament::input.select>
                                </x-filament::input.wrapper>
                            </div>

                            {{-- Q15 --}}
                        @elseif($i == 15)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    15. ધંધો/નોકરી કયા પ્રકાર ની કરો છો જેમાં ?
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @php
                                        $q15Opts = [
                                            'SettingJob' => 'બેસવા વધુ રહે (Sitting job)',
                                            'StandingJob' => 'ઉભા રહેવા નુ વધુ રહે (Standing job)',
                                            'TravellingJob' => 'મુસાફરી વધારે રહે (Travelling job)',
                                            'SunLightJob' => 'તડકા મા (In Sunlight)',
                                            'SettingRoomJob' => 'છાયા મા (In Shade)',
                                            'AcJob' => 'એસી મા (In AC)'
                                        ];
                                    @endphp
                                    @foreach($q15Opts as $key => $lbl)
                                        <label
                                            class="flex items-center gap-3 p-3 rounded-lg border dark:border-white/5 cursor-pointer">
                                            <x-filament::input.checkbox x-model="responses.Question15_{{ $key }}"/>
                                            <span class="text-sm">{{ $lbl }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Q16 --}}
                        @elseif($i == 16)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    16. સવારે 10-11 વાગ્યે ફ્રુટ કે બીજી કઈ વસ્તુ ખાવ છો ?
                                </label>
                                <div class="flex gap-8 mb-2">
                                    @foreach(['yes', 'no'] as $opt)
                                        <label class="flex items-center gap-2">
                                            <x-filament::input.radio value="{{ $opt }}"
                                                                     x-model="responses.Question16_FruitYesNo"/>
                                            <span class="text-sm">{{ ucfirst($opt) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <x-filament::input.wrapper>
                                    <x-filament::input type="text" placeholder="Enter Name of Fruit or Other"
                                                       x-model="responses.Question16_Fruits"/>
                                </x-filament::input.wrapper>
                            </div>

                            {{-- Q17 --}}
                        @elseif($i == 17)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    17. બોપરે કેટલા વાગે જમો છો ?
                                </label>
                                <x-filament::input.wrapper class="max-w-xs">
                                    <x-filament::input type="time" x-model="responses.Question17_LunchTime"/>
                                </x-filament::input.wrapper>
                            </div>

                            {{-- Q18 --}}
                        @elseif($i == 18)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    18. બપોરે ભૂખ અને જમવાનો સમય
                                </label>
                                <div
                                    class="space-y-4 p-4 rounded-xl bg-gray-50 dark:bg-white/5 border dark:border-white/5">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <span
                                            class="text-sm font-medium">બોપરે ભૂખ લાગી હોય છે ? (Hungry at Lunch?)</span>
                                        <div class="flex gap-4">
                                            <label class="flex items-center gap-2">
                                                <x-filament::input.radio value="yes"
                                                                         x-model="responses.Question18_LunchHugreyYesNo"/>
                                                <span class="text-sm">Yes</span></label>
                                            <label class="flex items-center gap-2">
                                                <x-filament::input.radio value="no"
                                                                         x-model="responses.Question18_LunchHugreyYesNo"/>
                                                <span class="text-sm">No</span></label>
                                        </div>
                                    </div>
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <span class="text-sm font-medium">કે સમય થયો એટલે જમો છો ? (Eating because of routine?)</span>
                                        <div class="flex gap-4">
                                            <label class="flex items-center gap-2">
                                                <x-filament::input.radio value="yes"
                                                                         x-model="responses.Question18_TimeLunchYesNo"/>
                                                <span class="text-sm">Yes</span></label>
                                            <label class="flex items-center gap-2">
                                                <x-filament::input.radio value="no"
                                                                         x-model="responses.Question18_TimeLunchYesNo"/>
                                                <span class="text-sm">No</span></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Q19 --}}
                        @elseif($i == 19)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    19. બપોરે શુ જમો છો ?
                                </label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                                    @php
                                        $q19 = ['Guvar'=>'ગુવાર','Brijal'=>'રિગાણા','Tamato'=>'ટામેટા','Patato'=>'બટેટા','LadyFinger'=>'ભીંડો','Chana'=>'ચણા','Val'=>'વાલ','Vatana'=>'વટાણા','Adad'=>'અડદ','AdadPapad'=>'અડદ પાપડ','Dhosa'=>'ઢોસા','Marcha'=>'મરચા','ButterMilk'=>'છાશ','Curd'=>'દહીં','SugerCane'=>'ગોળ','Athanu'=>'અથાણું','DalBhat'=>'દાળ ભાત'];
                                    @endphp
                                    @foreach($q19 as $key => $lbl)
                                        <label
                                            class="flex items-center gap-2 p-1.5 rounded hover:bg-gray-50 dark:hover:bg-white/5 transition-colors cursor-pointer">
                                            <x-filament::input.checkbox x-model="responses.Question19_{{ $key }}"/>
                                            <span class="text-sm">{{ $lbl }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Q20 --}}
                        @elseif($i == 20)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    20. દરરોજ અથવા વધારે શુ લો છો ?
                                </label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    @php
                                        $q20 = ['Gol'=>'ગોળ','Curd'=>'દહીં','ButterMilk'=>'છાસ','Athanu'=>'અથાણું','Spicies'=>'મરચા','Chatani'=>'ચટણી','Garlic'=>'લસણ','Onion'=>'ડુંગળી','PalkhniBhaji'=>'પાલખ','AdadPapad'=>'પાપડ','Rices'=>'ભાત','Tikhu'=>'તીખું','Khatu'=>'ખાટુ','Sour'=>'ખારું','KoroNasto'=>'કોરો નાસ્તો','LunchSleep'=>'બપોર નિદ્રા','LatenightWakeup'=>'રાત્રી જાગરણ','Sweet'=>'મીઠાઈ'];
                                    @endphp
                                    @foreach($q20 as $key => $lbl)
                                        <label
                                            class="flex items-center gap-2 p-1.5 rounded hover:bg-gray-50 dark:hover:bg-white/5 transition-colors cursor-pointer">
                                            <x-filament::input.checkbox x-model="responses.Question20_{{ $key }}"/>
                                            <span class="text-sm">{{ $lbl }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Q21 --}}
                        @elseif($i == 21)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    21. દરરોજ ની ટેવ જેમકે બપોરે દરરોજ સૂવું, ખારીસીંગ કે દાળિયા ખાવા, ભાત ખાવા, સોડા કે
                                    આઇશક્રેમ ખાવો, ફ્રેઝ નુ કે બોટલ નુ ઠંડુ પાણી પીવું કે દરરોજ ફ્રુટ ખાવા ?
                                </label>
                                <x-filament::input.wrapper>
                                    <x-filament::input type="text" placeholder="વર્ણન કરો (Describe Habits)"
                                                       x-model="responses.Question21_Details"/>
                                </x-filament::input.wrapper>
                            </div>

                            {{-- Q22 --}}
                        @elseif($i == 22)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    22. કાચા શાકભાજી ખાવ છો ?
                                </label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    @php
                                        $q22 = ['Cabbage'=>'કોબી','Kakadi'=>'કાકડી','Tomato'=>'ટામેટા','Carrot'=>'ગાજર','LiliHatdar'=>'લીલી હળદળ','LadyFinger'=>'ભીંડો','SweetPatoto'=>'શકરિયા','LilaChana'=>'લીલા ચાના','LilaVatana'=>'લીલા વટાણા','LilaTuver'=>'લીલી તુવેર','LilaNuts'=>'લીલી સીંગ'];
                                    @endphp
                                    @foreach($q22 as $key => $lbl)
                                        <label
                                            class="flex items-center gap-2 p-1.5 rounded hover:bg-gray-50 dark:hover:bg-white/5 transition-colors cursor-pointer">
                                            <x-filament::input.checkbox x-model="responses.Question22_{{ $key }}"/>
                                            <span class="text-sm">{{ $lbl }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Q23 --}}
                        @elseif($i == 23)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    23. શાક માં લસણ,ડુંગળી,ટમેટો,લીંબુ,ગોળ નાખો છો ?
                                </label>
                                <div class="flex gap-8">
                                    @foreach(['yes', 'no'] as $opt)
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <x-filament::input.radio value="{{ $opt }}"
                                                                     x-model="responses.Question23_VegitableInvalidYesNo"/>
                                            <span class="text-sm">{{ ucfirst($opt) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Q24 --}}
                        @elseif($i == 24)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    24. જમ્યા પછી કઈ ખાવા-પીવા ની ટેવ છે ?
                                </label>
                                <div class="flex gap-8 mb-3">
                                    @foreach(['yes', 'no'] as $opt)
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <x-filament::input.radio value="{{ $opt }}"
                                                                     x-model="responses.Question24_AfterEatingYesNo"/>
                                            <span class="text-sm">{{ ucfirst($opt) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <div x-show="responses.Question24_AfterEatingYesNo == 'yes'" x-transition
                                     class="grid grid-cols-2 sm:grid-cols-3 gap-2 pt-2 border-t dark:border-white/5">
                                    @php
                                        $q24 = ['Mukhvas'=>'મુખવાસ','Nuts'=>'ખારીશીંગ','Daliya'=>'દાળિયા','Vatana'=>'વટાણા','Icecream'=>'આઇશક્રેમ','Fruit'=>'ફ્રૂટ','Soda'=>'સોડા'];
                                    @endphp
                                    @foreach($q24 as $key => $lbl)
                                        <label
                                            class="flex items-center gap-2 p-1.5 rounded hover:bg-gray-50 dark:hover:bg-white/5 cursor-pointer">
                                            <x-filament::input.checkbox x-model="responses.Question24_{{ $key }}"/>
                                            <span class="text-sm">{{ $lbl }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Q25 --}}
                        @elseif($i == 25)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    25. ચાયનીઝ,સમોસા,ખમણ,પફ,સોડા,આઇશક્રેમ,બેકરી ની બનાવટ,બટાકા ભૂંગળા વગેરે ખાવ છો?
                                </label>
                                <div class="flex gap-8">
                                    @foreach(['yes', 'no'] as $opt)
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <x-filament::input.radio value="{{ $opt }}"
                                                                     x-model="responses.Question25_JunkFoodYesNo"/>
                                            <span class="text-sm">{{ ucfirst($opt) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Q26 --}}
                        @elseif($i == 26)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    26. બપોરે શુવો છો ?
                                </label>
                                <div class="flex gap-8 mb-2">
                                    @foreach(['yes', 'no'] as $opt)
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <x-filament::input.radio value="{{ $opt }}"
                                                                     x-model="responses.Question26_SleepAtNoon"/>
                                            <span class="text-sm">{{ ucfirst($opt) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <x-filament::input.wrapper class="max-w-xs">
                                    <x-filament::input type="text" placeholder="Time In Hour"
                                                       x-model="responses.Question26_TimeInHour"/>
                                </x-filament::input.wrapper>
                            </div>

                            {{-- Q27 --}}
                        @elseif($i == 27)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    27. બપોરે જાગ્યા પછી ચા/કોફી કે નાસ્તો કે ફ્રૂટ કે ફ્રૂટ જ્યુશ ખાવ છો ?
                                </label>
                                <div class="flex gap-8 mb-2">
                                    @foreach(['yes', 'no'] as $opt)
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <x-filament::input.radio value="{{ $opt }}"
                                                                     x-model="responses.Question27_LunchAfterTea"/>
                                            <span class="text-sm">{{ ucfirst($opt) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <x-filament::input.wrapper>
                                    <x-filament::input type="text" placeholder="Name of Things"
                                                       x-model="responses.Question27_Names"/>
                                </x-filament::input.wrapper>
                            </div>

                            {{-- Q28 --}}
                        @elseif($i == 28)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    28. સાંજે કેટલા વાગે જમો છો ?
                                </label>
                                <x-filament::input.wrapper class="max-w-xs">
                                    <x-filament::input type="time" x-model="responses.Question28_EveningDinner"/>
                                </x-filament::input.wrapper>
                            </div>

                            {{-- Q29 --}}
                        @elseif($i == 29)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    29. સાંજનું ભોજન (Evening Meal Details)
                                </label>
                                <div
                                    class="grid gap-4 p-4 rounded-xl bg-gray-50 dark:bg-white/5 border dark:border-white/5">
                                    <div class="space-y-1.5">
                                        <span class="text-sm font-medium">સાંજે જમવા શુ લો છે ?</span>
                                        <x-filament::input.wrapper>
                                            <x-filament::input type="text" x-model="responses.Question29_DinnerNames"/>
                                        </x-filament::input.wrapper>
                                    </div>
                                    <div class="space-y-1.5">
                                        <span class="text-sm font-medium">દૂધ કેટલુ લો છે ?</span>
                                        <x-filament::input.wrapper>
                                            <x-filament::input type="text" x-model="responses.Question29_MilksInMl"/>
                                        </x-filament::input.wrapper>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium">ખીચડી અને દૂધ ભેગું કરીને ખાવ છો ?</span>
                                        <div class="flex gap-4">
                                            <label class="flex items-center gap-2">
                                                <x-filament::input.radio value="yes"
                                                                         x-model="responses.Question29_KhichadiYesNo"/>
                                                <span class="text-sm">Yes</span></label>
                                            <label class="flex items-center gap-2">
                                                <x-filament::input.radio value="no"
                                                                         x-model="responses.Question29_KhichadiYesNo"/>
                                                <span class="text-sm">No</span></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Q30 --}}
                        @elseif($i == 30)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    30. સાંજે જમ્યા પછી રાત્રે નાસ્તો કે ફ્રૂટ કે દૂધ કે પાણી લો છો ?
                                </label>
                                <div class="flex gap-8">
                                    @foreach(['yes', 'no'] as $opt)
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <x-filament::input.radio value="{{ $opt }}"
                                                                     x-model="responses.Question30_AfterDinnerSnackYesNo"/>
                                            <span class="text-sm">{{ ucfirst($opt) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Q31 --}}
                        @elseif($i == 31)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    31. પાણી આખો દિવસ મા કેટલું પીવો છો ?
                                </label>
                                <x-filament::input.wrapper class="max-w-xs">
                                    <x-filament::input type="text" placeholder="Enter in ML"
                                                       x-model="responses.Question31_WaterInDay"/>
                                </x-filament::input.wrapper>
                            </div>

                            {{-- Q32 --}}
                        @elseif($i == 32)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    32. વિરોધ આહાર કરો છો ? (Incompatible Foods)
                                </label>
                                <div class="grid grid-cols-1 gap-2">
                                    @php
                                        $q32 = ['KhichadiMilk'=>'ખીચડી અને દૂધ','Garlic'=>'લસણ અને ડુંગળી કે ટામેટા ના શાક સાથે દૂધ પીવું','FruitMilk'=>'ફ્રૂટ સાથે દૂધ મિક્સ કરીને જ્યુશ લેવું','FruitSalad'=>'ફ્રૂટ સલાડ (મિલ્ક શેક )','ButterAndMilk'=>'છાસ અને દૂધ','ChatniWithMilk'=>'ચટણી સાથે દૂધ','HotWaterHoony'=>'ગરમ પાણી અને મધ','UnSeasonalFruit'=>'ઋતુ સિવાય ના ફળો બીજી ઋતુ માં લેવા','TakeFoodWithoutLatrine'=>'સંડાસ ગયા વગર જમો છો'];
                                    @endphp
                                    @foreach($q32 as $key => $lbl)
                                        <label
                                            class="flex items-center gap-3 p-2 rounded hover:bg-gray-50 dark:hover:bg-white/5 cursor-pointer">
                                            <x-filament::input.checkbox x-model="responses.Question32_{{ $key }}"/>
                                            <span class="text-sm leading-snug">{{ $lbl }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Q33 --}}
                        @elseif($i == 33)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    33. ફ્રૂટ ક્યા ખાવ છો ?
                                </label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                                    @php
                                        $q33 = ['Banana'=>'કેળા','Apple'=>'સફરજન','Graps'=>'લીલી/કાળી દ્રાક્ષ','WaterMeleon'=>'તરબૂચ','Coconut'=>'લીલું નાળિયર','Chiku'=>'ચીકુ','Pomegranate'=>'દાડમ','Mongo'=>'કેરી','Papiya'=>'પપેયો','Orange'=>'સંતરા/મોસંબી','Gooseberry'=>'જામફળ','Jambu'=>'જાંબુ','SweetTeti'=>'સાકર ટેટી','SugarCane'=>'શેરડી','Stroberry'=>'સ્ટ્રોબરી','Ambala'=>'આંબળા','Kiwi'=>'કીવી','DragoanFruit'=>'દ્રગન ફ્રૂટ','Pinnepal'=>'પાઈનેપલ'];
                                    @endphp
                                    @foreach($q33 as $key => $lbl)
                                        <label
                                            class="flex items-center gap-2 p-1.5 rounded hover:bg-gray-50 dark:hover:bg-white/5 cursor-pointer">
                                            <x-filament::input.checkbox x-model="responses.Question33_{{ $key }}"/>
                                            <span class="text-sm">{{ $lbl }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Q34 --}}
                        @elseif($i == 34)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    34. આ સિવાય ની એક એવી ટેવ જે દરરોજ કરો છો ?
                                </label>
                                <x-filament::input.wrapper>
                                    <x-filament::input type="text" placeholder="Name Of Habit"
                                                       x-model="responses.Question34_Habbit"/>
                                </x-filament::input.wrapper>
                            </div>

                            {{-- Q35 --}}
                        @elseif($i == 35)
                            <div class="space-y-4">
                                <label class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    35. મેડિકલ વિગતો (Medical Details)
                                </label>
                                <div
                                    class="grid gap-4 p-4 rounded-xl bg-gray-50 dark:bg-white/5 border dark:border-white/5">
                                    <div class="space-y-1.5">
                                        <span class="text-sm font-medium">બીજો કઈ રોગ છે ? (Other Disease)</span>
                                        <x-filament::input.wrapper>
                                            <x-filament::input type="text" placeholder="Disease Name"
                                                               x-model="responses.Question35_OtherDisease"/>
                                        </x-filament::input.wrapper>
                                    </div>
                                    <div class="space-y-1.5">
                                        <span class="text-sm font-medium">દવા કઈ ચાલુ છે ? (Current Medicines)</span>
                                        <x-filament::input.wrapper>
                                            <x-filament::input type="text" placeholder="Medicines"
                                                               x-model="responses.Question35_Medicines"/>
                                        </x-filament::input.wrapper>
                                    </div>
                                    <div class="space-y-1.5">
                                        <span class="text-sm font-medium">કેટલા વર્ષ કે મહિના થી? (Duration)</span>
                                        <x-filament::input.wrapper>
                                            <x-filament::input type="text" placeholder="Duration (Years/Months)"
                                                               x-model="responses.Question35_DiseaseTime"/>
                                        </x-filament::input.wrapper>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- RIGHT COLUMN: Analysis & Status --}}
                    <div class="w-full lg:w-1/2 bg-gray-50/50 dark:bg-white/5 p-6 gap-8 flex">
                        {{-- Dosha Analysis --}}
                        <section>
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Dosha
                                Analysis</h4>
                            <div class="gap-3 flex">
                                @foreach(['vat' => 'VAT', 'pit' => 'PIT', 'kuf' => 'KUF'] as $key => $lbl)
                                    <label
                                        class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 dark:border-white/10 bg-white dark:bg-gray-900 shadow-sm cursor-pointer group hover:border-primary-500 transition-all">
                                        <x-filament::input.checkbox x-model="responses.q{{$i}}_{{ $key }}"/>
                                        <span
                                            class="text-sm font-bold text-gray-600 dark:text-gray-400 group-hover:text-primary-600">{{ $lbl }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </section>


                        {{-- Benefit Status --}}
                        <section>
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">
                                Classification</h4>
                            <div class="flex gap-3">
                                <label
                                    class="flex items-center gap-3 p-3 rounded-xl border border-transparent cursor-pointer transition-all hover:ring-2 hover:ring-green-500/20"
                                    :class="responses.q{{$i}}_status === 'HitKar' ? 'bg-green-50 dark:bg-green-500/10 border-green-200 dark:border-green-500/30' : 'bg-white dark:bg-gray-900 border-gray-100 dark:border-white/10'">
                                    <x-filament::input.radio value="HitKar" name="q{{$i}}_st"
                                                             x-model="responses.q{{$i}}_status"/>
                                    <span class="text-sm font-bold text-green-600 dark:text-green-500">હિતકર (Beneficial)</span>
                                </label>

                                <label
                                    class="flex items-center gap-3 p-3 rounded-xl border border-transparent cursor-pointer transition-all hover:ring-2 hover:ring-red-500/20"
                                    :class="responses.q{{$i}}_status === 'AhitKar' ? 'bg-red-50 dark:bg-red-500/10 border-red-200 dark:border-red-500/30' : 'bg-white dark:bg-gray-900 border-gray-100 dark:border-white/10'">
                                    <x-filament::input.radio value="AhitKar" name="q{{$i}}_st"
                                                             x-model="responses.q{{$i}}_status"/>
                                    <span
                                        class="text-sm font-bold text-red-600 dark:text-red-500">અહિતકર (Harmful)</span>
                                </label>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- STICKY SUMMARY FOOTER --}}
        <div class="sticky bottom-6 z-30 mx-auto w-full max-w-5xl px-4">
            <div
                class="rounded-2xl bg-white/90 dark:bg-gray-900/95 backdrop-blur-md border border-gray-200 dark:border-white/10 shadow-2xl p-6 ring-1 ring-gray-950/5">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-sm font-black text-gray-950 dark:text-white uppercase tracking-widest">
                            Diagnostic Summary</h3>
                        <p class="text-[10px] text-gray-500 font-medium">Aggregate Analysis across all 35 parameters</p>
                    </div>
                    <div class="hidden sm:block">
                        <span
                            class="inline-flex items-center rounded-full bg-primary-50 dark:bg-primary-500/10 px-2 py-1 text-xs font-medium text-primary-700 dark:text-primary-400 ring-1 ring-inset ring-primary-700/10">Live Calculation</span>
                    </div>
                </div>

                <div class="grid grid-cols-3 md:grid-cols-5 gap-4">
                    @foreach(['vat' => 'VAT', 'pit' => 'PIT', 'kuf' => 'KUF'] as $key => $lbl)
                        <div
                            class="p-4 rounded-2xl bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/5 text-center transition-all hover:scale-[1.02]">
                            <div
                                class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter mb-1">{{ $lbl }}</div>
                            <div class="text-3xl font-black text-primary-600 dark:text-primary-400"
                                 x-text="totals.{{$key}}">0
                            </div>
                        </div>
                    @endforeach

                    <div
                        class="p-4 rounded-2xl bg-green-50 dark:bg-green-500/10 border border-green-100 dark:border-green-500/20 text-center transition-all hover:scale-[1.02]">
                        <div
                            class="text-[10px] font-bold text-green-600/70 dark:text-green-500/70 uppercase tracking-tighter mb-1">
                            HitKar
                        </div>
                        <div class="text-3xl font-black text-green-700 dark:text-green-500" x-text="totals.hit">0</div>
                    </div>

                    <div
                        class="p-4 rounded-2xl bg-red-50 dark:bg-red-500/10 border border-red-100 dark:border-red-500/20 text-center transition-all hover:scale-[1.02]">
                        <div
                            class="text-[10px] font-bold text-red-600/70 dark:text-red-500/70 uppercase tracking-tighter mb-1">
                            AhitKar
                        </div>
                        <div class="text-3xl font-black text-red-700 dark:text-red-500" x-text="totals.ahit">0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
