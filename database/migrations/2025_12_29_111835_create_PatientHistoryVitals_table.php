<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('PatientHistoryVitals', function (Blueprint $table) {
            $table->uuid('Id');
            $table->text('BodyTemperature')->nullable();
            $table->text('PluseRate')->nullable();
            $table->text('RespirationRate')->nullable();
            $table->text('BloodPressure')->nullable();
            $table->text('DiabetesCount')->nullable();
            $table->uuid('PatientHistoryId');
            $table->text('Spo2')->nullable();

            $table->primary(['Id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PatientHistoryVitals');
    }
};
