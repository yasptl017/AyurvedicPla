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
        Schema::table('PatientHistoryMedicines', function (Blueprint $table) {
            $table->foreign(['MedicineId'])->references(['Id'])->on('Medicines')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['PatientHistoryId'])->references(['Id'])->on('PatientHistories')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('PatientHistoryMedicines', function (Blueprint $table) {
            $table->dropForeign('patienthistorymedicines_medicineid_foreign');
            $table->dropForeign('patienthistorymedicines_patienthistoryid_foreign');
        });
    }
};
