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
        Schema::table('AstavidhyaParikshas', function (Blueprint $table) {
            // Mala options moved into Nadi field; drop the separate column
            $table->dropColumn('Mala');

            // Mutra changed from free-text textarea to a radio (string) field
            $table->string('Mutra')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('AstavidhyaParikshas', function (Blueprint $table) {
            $table->string('Mala')->nullable();
            $table->text('Mutra')->nullable()->change();
        });
    }
};
