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
        Schema::create('__EFMigrationsHistory', function (Blueprint $table) {
            $table->string('MigrationId', 150);
            $table->string('ProductVersion', 32);

            $table->primary(['MigrationId']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('__EFMigrationsHistory');
    }
};
