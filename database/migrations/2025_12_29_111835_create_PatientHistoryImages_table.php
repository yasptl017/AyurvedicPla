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
        Schema::create('PatientHistoryImages', function (Blueprint $table) {
            $table->uuid('PatientHistoryImageId');
            $table->uuid('PatientHistoryId');
            $table->text('Src')->nullable();
            $table->dateTime('CreatedDate')->nullable();
            $table->uuid('CreatedBy');
            $table->dateTime('ModifiedDate')->nullable();
            $table->uuid('ModifiedBy');
            $table->dateTime('DeletedDate')->nullable();
            $table->uuid('DeletedBy');
            $table->boolean('IsDeleted');

            $table->primary(['PatientHistoryImageId']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PatientHistoryImages');
    }
};
