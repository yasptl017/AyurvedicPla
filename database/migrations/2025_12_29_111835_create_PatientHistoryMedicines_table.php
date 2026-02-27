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
        Schema::create('PatientHistoryMedicines', function (Blueprint $table) {
            $table->uuid('Id');
            $table->uuid('PatientHistoryId');
            $table->unsignedInteger('MedicineId');
            $table->text('Dose')->nullable();
            $table->string('TimeOfAdministration', 450)->nullable();
            $table->string('Duration', 450)->nullable();
            $table->text('Anupana')->nullable();
            $table->text('MedicineFormName')->nullable();
            $table->dateTime('CreatedDate')->nullable();
            $table->uuid('CreatedBy');
            $table->dateTime('ModifiedDate')->nullable()->useCurrent();
            $table->uuid('ModifiedBy');
            $table->dateTime('DeletedDate')->nullable();
            $table->uuid('DeletedBy')->nullable();
            $table->boolean('IsDeleted')->nullable();
            $table->text('Amount')->nullable();

            $table->primary(['Id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PatientHistoryMedicines');
    }
};
