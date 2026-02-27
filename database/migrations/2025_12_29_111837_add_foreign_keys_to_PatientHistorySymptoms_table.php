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
        Schema::table('PatientHistorySymptoms', function (Blueprint $table) {
            $table->foreign(['PatientHistoryId'])->references(['Id'])->on('PatientHistories')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['SymptomId'])->references(['Id'])->on('Symptoms')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('PatientHistorySymptoms', function (Blueprint $table) {
            $table->dropForeign('patienthistorysymptoms_patienthistoryid_foreign');
            $table->dropForeign('patienthistorysymptoms_symptomid_foreign');
        });
    }
};
