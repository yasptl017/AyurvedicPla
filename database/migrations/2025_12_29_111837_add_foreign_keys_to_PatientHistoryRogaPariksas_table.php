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
        Schema::table('PatientHistoryRogaPariksas', function (Blueprint $table) {
            $table->foreign(['PatientHistoryId'])->references(['Id'])->on('PatientHistories')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['RogaPariksaId'])->references(['Id'])->on('RogaPariksas')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('PatientHistoryRogaPariksas', function (Blueprint $table) {
            $table->dropForeign('patienthistoryrogapariksas_patienthistoryid_foreign');
            $table->dropForeign('patienthistoryrogapariksas_rogapariksaid_foreign');
        });
    }
};
