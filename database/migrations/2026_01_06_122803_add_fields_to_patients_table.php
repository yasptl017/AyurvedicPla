<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('Patients', function (Blueprint $table) {
            $table->text('sketch')->nullable();
            $table->string('complain_of')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('Patients', function (Blueprint $table) {

            $table->dropColumn('sketch');
            $table->dropColumn('complain_of');
        });
    }
};
