<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('awaiting_patient_entries', function (Blueprint $table) {
            $table->increments('Id');
            $table->dateTime('CreatedDate')->nullable();
            $table->uuid('CreatedBy')->nullable();
            $table->dateTime('ModifiedDate')->nullable()->useCurrent();
            $table->uuid('ModifiedBy')->nullable();
            $table->dateTime('DeletedDate')->nullable();
            $table->uuid('DeletedBy')->nullable();
            $table->boolean('IsDeleted')->nullable();
            $table->unsignedInteger('ClinicId');
            $table->unsignedInteger('PatientId');
            $table->date('QueueDate');

            $table->index(['ClinicId', 'QueueDate']);
            $table->index(['PatientId', 'QueueDate']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('awaiting_patient_entries');
    }
};
