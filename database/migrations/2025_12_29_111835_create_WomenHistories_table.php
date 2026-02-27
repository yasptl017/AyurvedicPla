<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('WomenHistories', function (Blueprint $table) {
            $table->uuid('Id');
            $table->text('Chief_complaint')->nullable();
            $table->dateTime('First_menstrual_period')->nullable();
            $table->text('Duration')->nullable();
            $table->text('Regular_irregular')->nullable();
            $table->text('Painful_painless')->nullable();
            $table->text('Scanty_moderate_excessive')->nullable();
            $table->text('Pads_used_per_day')->nullable();
            $table->text('Local_examination')->nullable();
            $table->text('P_s')->nullable();
            $table->text('P_v')->nullable();
            $table->text('Coital_history')->nullable();
            $table->text('Stool_regular_irregular')->nullable();
            $table->text('Urine_normal_abnormal')->nullable();
            $table->text('Appetizer_regular_irregular')->nullable();
            $table->text('Sleep_regular_irregular')->nullable();
            $table->text('Full_term_yes_no')->nullable();
            $table->text('Pre_term_yes_no')->nullable();
            $table->text('Lower_segment_yes_no')->nullable();
            $table->text('Forcep_yes_no')->nullable();
            $table->dateTime('Expected_delivery_date')->nullable();
            $table->text('Other')->nullable();
            $table->text('Dead')->nullable();
            $table->text('Live')->nullable();
            $table->text('Abortion')->nullable();
            $table->text('Parity')->nullable();
            $table->text('Gravida')->nullable();
            $table->dateTime('Last_delivery')->nullable();
            $table->dateTime('Last_menstrual_period')->nullable();
            $table->text('Vacum_yes_no')->nullable();
            $table->text('Bp')->nullable();
            $table->text('Pulse')->nullable();
            $table->text('Weight')->nullable();
            $table->text('Investigations')->nullable();
            $table->text('Contraceptive_history')->nullable();
            $table->uuid('PatientHistoryId');

            $table->primary(['Id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('WomenHistories');
    }
};
