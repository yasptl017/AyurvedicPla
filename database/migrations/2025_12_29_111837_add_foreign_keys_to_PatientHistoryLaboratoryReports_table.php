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
        Schema::table('PatientHistoryLaboratoryReports', function (Blueprint $table) {
            $table->foreign(['LaboratoryReportId'])->references(['Id'])->on('LaboratoryReports')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['PatientHistoryId'])->references(['Id'])->on('PatientHistories')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('PatientHistoryLaboratoryReports', function (Blueprint $table) {
            $table->dropForeign('patienthistorylaboratoryreports_laboratoryreportid_foreign');
            $table->dropForeign('patienthistorylaboratoryreports_patienthistoryid_foreign');
        });
    }
};
