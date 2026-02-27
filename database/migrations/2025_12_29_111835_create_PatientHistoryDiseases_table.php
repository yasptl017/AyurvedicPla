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
        Schema::create('PatientHistoryDiseases', function (Blueprint $table) {
            $table->uuid('Id');
            $table->unsignedInteger('DiseaseId');
            $table->unsignedInteger('DiseaseTypeId')->nullable();
            $table->uuid('PatientHistoryId');
            $table->dateTime('CreatedDate')->nullable();
            $table->uuid('CreatedBy')->nullable();
            $table->dateTime('ModifiedDate')->nullable();
            $table->uuid('ModifiedBy')->nullable();
            $table->dateTime('DeletedDate')->nullable();
            $table->uuid('DeletedBy')->nullable();
            $table->boolean('IsDeleted')->nullable();

            $table->primary(['Id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PatientHistoryDiseases');
    }
};
