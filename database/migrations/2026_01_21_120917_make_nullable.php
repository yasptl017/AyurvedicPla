<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('PatientHistories', function (Blueprint $table) {
            $table->float('ConsultationFee')->nullable()->change();
            $table->float('MedicinesFee')->nullable()->change();
        });
    }

};
