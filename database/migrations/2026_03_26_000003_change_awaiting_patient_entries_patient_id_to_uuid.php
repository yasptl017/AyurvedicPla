<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE awaiting_patient_entries MODIFY PatientId CHAR(36) NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE awaiting_patient_entries MODIFY PatientId INT UNSIGNED NOT NULL');
    }
};
