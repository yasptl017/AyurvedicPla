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
        Schema::create('DiseaseTypeLaboratoryReports', function (Blueprint $table) {
            $table->unsignedInteger('LaboratoryReportId');
            $table->unsignedInteger('DiseaseTypeId');
            $table->dateTime('CreatedDate')->nullable();
            $table->uuid('CreatedBy');
            $table->dateTime('ModifiedDate')->nullable();
            $table->uuid('ModifiedBy');
            $table->dateTime('DeletedDate')->nullable();
            $table->uuid('DeletedBy');
            $table->boolean('IsDeleted');
            $table->boolean('IsSpecial')->default(false);

            $table->primary(['DiseaseTypeId', 'LaboratoryReportId']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('DiseaseTypeLaboratoryReports');
    }
};
