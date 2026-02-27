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
        Schema::table('sketches', function (Blueprint $table) {
            $table->uuid('patient_history_id')->nullable()->after('Patient_id');
            $table->foreign('patient_history_id')->references('Id')->on('PatientHistories')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sketches', function (Blueprint $table) {
            $table->dropForeign(['patient_history_id']);
            $table->dropColumn('patient_history_id');
        });
    }
};
