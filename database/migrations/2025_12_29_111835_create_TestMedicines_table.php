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
        Schema::create('TestMedicines', function (Blueprint $table) {
            $table->increments('Id');
            $table->text('Name')->nullable();
            $table->text('Description')->nullable();
            $table->text('URL')->nullable();

            $table->primary(['Id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TestMedicines');
    }
};
