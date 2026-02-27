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
        Schema::table('PatientHistoryDiseases', function (Blueprint $table) {
            $table->foreign(['DiseaseId'])->references(['Id'])->on('Diseases')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['DiseaseTypeId'])->references(['Id'])->on('DiseaseTypes')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['PatientHistoryId'])->references(['Id'])->on('PatientHistories')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('PatientHistoryDiseases', function (Blueprint $table) {
            $table->dropForeign('patienthistorydiseases_diseaseid_foreign');
            $table->dropForeign('patienthistorydiseases_diseasetypeid_foreign');
            $table->dropForeign('patienthistorydiseases_patienthistoryid_foreign');
        });
    }
};
