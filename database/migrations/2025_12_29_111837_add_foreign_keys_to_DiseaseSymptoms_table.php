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
        Schema::table('DiseaseSymptoms', function (Blueprint $table) {
            $table->foreign(['DiseaseId'])->references(['Id'])->on('Diseases')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['SymptomId'])->references(['Id'])->on('Symptoms')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('DiseaseSymptoms', function (Blueprint $table) {
            $table->dropForeign('diseasesymptoms_diseaseid_foreign');
            $table->dropForeign('diseasesymptoms_symptomid_foreign');
        });
    }
};
