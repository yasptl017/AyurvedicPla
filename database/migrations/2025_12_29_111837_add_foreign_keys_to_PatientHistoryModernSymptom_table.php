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
        Schema::table('PatientHistoryModernSymptom', function (Blueprint $table) {
            $table->foreign(['SymptomId'])->references(['Id'])->on('ModernSymptoms')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['PatientHistoryId'])->references(['Id'])->on('PatientHistories')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('PatientHistoryModernSymptom', function (Blueprint $table) {
            $table->dropForeign('patienthistorymodernsymptom_symptomid_foreign');
            $table->dropForeign('patienthistorymodernsymptom_patienthistoryid_foreign');
        });
    }
};
