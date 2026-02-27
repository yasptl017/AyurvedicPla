<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('Patients', function (Blueprint $table) {
            $table->uuid('DeletedBy')->default("00000000-0000-0000-0000-000000000000");
            $table->boolean('IsDeleted')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('DeletedBy');
            $table->dropColumn('IsDeleted');
        });
    }
};
