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
        Schema::table('DiseaseTypeSymptoms', function (Blueprint $table) {
            $table->foreign(['DiseaseTypeId'])->references(['Id'])->on('DiseaseTypes')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['SymptomId'])->references(['Id'])->on('Symptoms')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('DiseaseTypeSymptoms', function (Blueprint $table) {
            $table->dropForeign('diseasetypesymptoms_diseasetypeid_foreign');
            $table->dropForeign('diseasetypesymptoms_symptomid_foreign');
        });
    }
};
