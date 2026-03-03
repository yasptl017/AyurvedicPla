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
        Schema::table('Patients', function (Blueprint $table) {
            $table->text('history_of')->nullable()->after('complain_of');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Patients', function (Blueprint $table) {
            $table->dropColumn('history_of');
        });
    }
};
