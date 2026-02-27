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
        Schema::table('DiseaseTypeLaboratoryReports', function (Blueprint $table) {
            $table->foreign(['DiseaseTypeId'])->references(['Id'])->on('DiseaseTypes')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['LaboratoryReportId'])->references(['Id'])->on('LaboratoryReports')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('DiseaseTypeLaboratoryReports', function (Blueprint $table) {
            $table->dropForeign('diseasetypelaboratoryreports_diseasetypeid_foreign');
            $table->dropForeign('diseasetypelaboratoryreports_laboratoryreportid_foreign');
        });
    }
};
