<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('RogaPariksas', function (Blueprint $table) {
            $table->uuid('patient_history_id')->nullable();
            $table->foreign('patient_history_id')->references('Id')->on('PatientHistories')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('RogaPariksas', function (Blueprint $table) {
            $table->dropForeign(['patient_history_id']);
            $table->dropColumn('patient_history_id');
        });
    }
};
