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
        Schema::create('PatientHistoryPanchakarmas', function (Blueprint $table) {
            $table->uuid('Id');
            $table->uuid('PatientHistoryId');
            $table->unsignedInteger('PanchakarmaId');
            $table->text('Detail')->nullable();
            $table->dateTime('CreatedDate')->nullable();
            $table->uuid('CreatedBy');
            $table->dateTime('ModifiedDate')->nullable();
            $table->uuid('ModifiedBy');
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
        Schema::dropIfExists('PatientHistoryPanchakarmas');
    }
};
