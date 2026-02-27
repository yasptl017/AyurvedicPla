<?php

use App\Models\Patient;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_records', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Patient::class)->constrained('Patients');
            $table->uuid('patient_history_id')->nullable();
            $table->foreign('patient_history_id')->references('Id')->on('PatientHistories')->nullOnDelete();
            $table->string('capture');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_records');
    }
};
