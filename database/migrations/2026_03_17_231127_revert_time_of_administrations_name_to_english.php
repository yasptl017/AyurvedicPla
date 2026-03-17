<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** @var array<int|string, string> */
    private array $englishNames = [
        1 => 'Before Meal',
        2 => 'Before Meals Once Daily',
        3 => 'Twice a Day',
        4 => 'Thrice Daily',
        5 => 'Four Time Daily',
        6 => 'Once in Morning',
        7 => 'Three time',
        8 => 'Week',
        9 => 'Before Meal / Thrice Daily',
        10 => 'Before Meals / Twice a Day',
        11 => '2 to 3 time',
        12 => 'Twice or Thrice a Day',
        13 => 'After Meal',
        14 => 'At Night',
        15 => 'Nil',
        16 => '4-5 times as Pratimarsha Nasya',
        17 => 'For External Application',
        18 => 'Once or Twice daily',
        19 => 'lozenges',
        20 => 'After meal / Twice a day',
        21 => 'Once at Bed Time',
        22 => 'As per Disease',
        23 => 'With Meal',
        1022 => 'After meal /thrice daily',
        1023 => 'Once at bed time or empty stomach early morning',
        1024 => 'Whole day – to quench the thirst',
        1025 => 'puran',
        1026 => 'Early morning empty stomach',
        1027 => '30 MIN',
        1028 => 'Once in a day',
        1029 => 'Every 30 Minute 1 Hour Punah Punah',
    ];

    public function up(): void
    {
        foreach ($this->englishNames as $id => $name) {
            DB::table('TimeOfAdministrations')
                ->where('Id', $id)
                ->update(['Name' => $name]);
        }
    }

    public function down(): void
    {
        // Intentionally empty — this migration only restores English names.
    }
};
