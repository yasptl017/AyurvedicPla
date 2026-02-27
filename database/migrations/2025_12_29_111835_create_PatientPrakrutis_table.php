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
        Schema::create('PatientPrakrutis', function (Blueprint $table) {
            $table->uuid('Id');
            $table->uuid('PatientId');
            $table->integer('VatCount');
            $table->integer('KufCount');
            $table->integer('PitCount');
            $table->integer('VatPercentage');
            $table->integer('PitPercentage');
            $table->integer('KufPercentage');
            $table->integer('Total');
            $table->dateTime('CreatedDate')->nullable();
            $table->uuid('CreatedBy');
            $table->dateTime('ModifiedDate')->nullable();
            $table->uuid('ModifiedBy');
            $table->dateTime('DeletedDate')->nullable();
            $table->uuid('DeletedBy');
            $table->boolean('IsDeleted');

            $table->primary(['Id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PatientPrakrutis');
    }
};
