<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** @var array<int|string, array{en: string, gu: string}> */
    private array $translations = [
        1 => ['en' => 'Before Meal',                                        'gu' => 'જમ્યા પહેલા'],
        2 => ['en' => 'Before Meals Once Daily',                            'gu' => 'જમ્યા પહેલા - એક વખત'],
        3 => ['en' => 'Twice a Day',                                        'gu' => 'બે વખત'],
        4 => ['en' => 'Thrice Daily',                                       'gu' => 'ત્રણ વખત'],
        5 => ['en' => 'Four Time Daily',                                    'gu' => 'ચાર વખત'],
        6 => ['en' => 'Once in Morning',                                    'gu' => 'સવારે એક વખત'],
        7 => ['en' => 'Three time',                                         'gu' => 'ત્રણ વખત'],
        8 => ['en' => 'Week',                                               'gu' => 'અઠવાડિયે'],
        9 => ['en' => 'Before Meal / Thrice Daily',                         'gu' => 'જમ્યા પહેલા - ત્રણ વખત'],
        10 => ['en' => 'Before Meals / Twice a Day',                        'gu' => 'જમ્યા પહેલા - બે વખત'],
        11 => ['en' => '2 to 3 time',                                       'gu' => 'બે થી ત્રણ વખત'],
        12 => ['en' => 'Twice or Thrice a Day',                             'gu' => 'બે અથવા ત્રણ વખત'],
        13 => ['en' => 'After Meal',                                        'gu' => 'જમ્યા પછી'],
        14 => ['en' => 'At Night',                                          'gu' => 'રાત્રે'],
        15 => ['en' => 'Nil',                                               'gu' => 'નહીં'],
        16 => ['en' => '4-5 times as Pratimarsha Nasya',                    'gu' => 'પ્રતિમર્શ નસ્ય - ૪-૫ વખત'],
        17 => ['en' => 'For External Application',                          'gu' => 'બાહ્ય ઉપયોગ માટે'],
        18 => ['en' => 'Once or Twice daily',                               'gu' => 'એક અથવા બે વખત'],
        19 => ['en' => 'lozenges',                                          'gu' => 'લોઝેન્જ'],
        20 => ['en' => 'After meal / Twice a day',                          'gu' => 'જમ્યા પછી - બે વખત'],
        21 => ['en' => 'Once at Bed Time',                                  'gu' => 'રાત્રે સૂતી વખતે'],
        22 => ['en' => 'As per Disease',                                    'gu' => 'રોગ પ્રમાણે'],
        23 => ['en' => 'With Meal',                                         'gu' => 'જમતી વખતે'],
        1022 => ['en' => 'After meal /thrice daily',                          'gu' => 'જમ્યા પછી - ત્રણ વખત'],
        1023 => ['en' => 'Once at bed time or empty stomach early morning',   'gu' => 'રાત્રે સૂતી વખતે અથવા સવારે ખાલી પેટ'],
        1024 => ['en' => 'Whole day – to quench the thirst',                  'gu' => 'આખો દિવસ - તરસ છીપાવવા'],
        1025 => ['en' => 'puran',                                             'gu' => 'પૂરણ'],
        1026 => ['en' => 'Early morning empty stomach',                       'gu' => 'સવારે ખાલી પેટ'],
        1027 => ['en' => '30 MIN',                                            'gu' => '૩૦ મિનિટ'],
        1028 => ['en' => 'Once in a day',                                     'gu' => 'દિવસ માં એક વખત'],
        1029 => ['en' => 'Every 30 Minute 1 Hour Punah Punah',               'gu' => 'દર ૩૦ મિનિટ - ૧ કલાક - પુનઃ પુનઃ'],
    ];

    public function up(): void
    {
        foreach ($this->translations as $id => $names) {
            DB::table('TimeOfAdministrations')
                ->where('Id', $id)
                ->update(['Name' => $names['gu']]);
        }
    }

    public function down(): void
    {
        foreach ($this->translations as $id => $names) {
            DB::table('TimeOfAdministrations')
                ->where('Id', $id)
                ->update(['Name' => $names['en']]);
        }
    }
};
