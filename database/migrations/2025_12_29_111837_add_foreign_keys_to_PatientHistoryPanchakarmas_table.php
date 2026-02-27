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
        Schema::table('PatientHistoryPanchakarmas', function (Blueprint $table) {
            $table->foreign(['PanchakarmaId'])->references(['Id'])->on('Panchakarmas')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['PatientHistoryId'])->references(['Id'])->on('PatientHistories')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('PatientHistoryPanchakarmas', function (Blueprint $table) {
            $table->dropForeign('patienthistorypanchakarmas_panchakarmaid_foreign');
            $table->dropForeign('patienthistorypanchakarmas_patienthistoryid_foreign');
        });
    }
};
