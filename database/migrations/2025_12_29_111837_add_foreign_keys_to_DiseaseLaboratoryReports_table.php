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
        Schema::table('DiseaseLaboratoryReports', function (Blueprint $table) {
            $table->foreign(['DiseaseId'])->references(['Id'])->on('Diseases')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['LaboratoryReportId'])->references(['Id'])->on('LaboratoryReports')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('DiseaseLaboratoryReports', function (Blueprint $table) {
            $table->dropForeign('diseaselaboratoryreports_diseaseid_foreign');
            $table->dropForeign('diseaselaboratoryreports_laboratoryreportid_foreign');
        });
    }
};
