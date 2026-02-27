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
        Schema::table('Medicines', function (Blueprint $table) {
            $table->foreign(['MedicineFormId'])->references(['Id'])->on('MedicineForms')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['ClinicId'])->references(['Id'])->on('Doctors')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Medicines', function (Blueprint $table) {
            $table->dropForeign('medicines_medicineformid_foreign');
            $table->dropForeign('medicines_clinicid_foreign');
        });
    }
};
