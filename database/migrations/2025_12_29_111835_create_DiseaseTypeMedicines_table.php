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
        Schema::create('DiseaseTypeMedicines', function (Blueprint $table) {
            $table->increments('Id');
            $table->dateTime('CreatedDate')->nullable();
            $table->uuid('CreatedBy');
            $table->dateTime('ModifiedDate')->nullable()->useCurrent();
            $table->uuid('ModifiedBy');
            $table->dateTime('DeletedDate')->nullable();
            $table->uuid('DeletedBy');
            $table->boolean('IsDeleted');
            $table->unsignedInteger('DiseaseTypeId');
            $table->unsignedInteger('MedicineId');
            $table->text('Dose')->nullable();
            $table->unsignedInteger('TimeOfAdministrationId');
            $table->unsignedInteger('AnupanaId');
            $table->text('Duration')->nullable();
            $table->boolean('IsSpecial')->default(false);
            $table->integer('OrderNumber')->nullable();
            $table->boolean('IsLevel3')->default(false);
            $table->boolean('IsLevel1')->default(false);
            $table->boolean('IsLevel2')->default(false);

            $table->primary(['Id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('DiseaseTypeMedicines');
    }
};
