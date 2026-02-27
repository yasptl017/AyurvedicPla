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
        Schema::create('DiseaseSymptoms', function (Blueprint $table) {
            $table->unsignedInteger('DiseaseId');
            $table->unsignedInteger('SymptomId');
            $table->boolean('IsMain')->default(true);

            $table->primary(['DiseaseId', 'SymptomId']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('DiseaseSymptoms');
    }
};
