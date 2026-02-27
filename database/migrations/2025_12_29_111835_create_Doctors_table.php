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
        Schema::create('Doctors', function (Blueprint $table) {
            $table->increments('Id');
            $table->dateTime('CreatedDate')->nullable();
            $table->uuid('CreatedBy')->nullable();
            $table->dateTime('ModifiedDate')->nullable()->useCurrent();
            $table->uuid('ModifiedBy')->nullable();
            $table->dateTime('DeletedDate')->nullable();
            $table->uuid('DeletedBy')->nullable();
            $table->boolean('IsDeleted')->nullable();
            $table->text('ClinicName')->nullable();
            $table->text('ClinicUrl')->nullable();
            $table->text('FirstName')->nullable();
            $table->text('LastName')->nullable();
            $table->text('Email')->nullable();
            $table->text('MobileNo')->nullable();
            $table->text('Address')->nullable();
            $table->unsignedInteger('CityId')->nullable();
            $table->unsignedInteger('StateId')->nullable();
            $table->text('Gender')->nullable();
            $table->text('PrescriptionUrl')->nullable();

            $table->primary(['Id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Doctors');
    }
};
