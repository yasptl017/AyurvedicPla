<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('Patients', function (Blueprint $table) {
            $table->string('consultation_fees_type')->default('Full')->after('complain_of');
        });
    }

    public function down(): void
    {
        Schema::table('Patients', function (Blueprint $table) {
            $table->dropColumn('consultation_fees_type');
        });
    }
};
