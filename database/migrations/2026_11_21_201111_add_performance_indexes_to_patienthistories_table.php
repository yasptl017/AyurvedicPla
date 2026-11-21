<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('patienthistories', function (Blueprint $table) {
            $table->index(['PatientId', 'CreatedDate'], 'patienthistories_patient_created_index');
            $table->index('CreatedDate', 'patienthistories_created_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patienthistories', function (Blueprint $table) {
            $table->dropIndex('patienthistories_patient_created_index');
            $table->dropIndex('patienthistories_created_index');
        });
    }
};
